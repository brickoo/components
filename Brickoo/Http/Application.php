<?php

    /*
     * Copyright (c) 2011-2012, Celestino Diaz <celestino.diaz@gmx.de>.
     * All rights reserved.
     *
     * Redistribution and use in source and binary forms, with or without
     * modification, are permitted provided that the following conditions
     * are met:
     *
     * 1. Redistributions of source code must retain the above copyright
     *    notice, this list of conditions and the following disclaimer.
     * 2. Redistributions in binary form must reproduce the above copyright
     *    notice, this list of conditions and the following disclaimer in the
     *    documentation and/or other materials provided with the distribution.
     * 3. Neither the name of Brickoo nor the names of its contributors may be used
     *    to endorse or promote products derived from this software without specific
     *    prior written permission.
     *
     * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
     * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
     * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
     * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
     * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
     * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
     * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
     * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
     * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
     * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
     * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
     */

    namespace Brickoo\Http;

    use Brickoo\Core\ApplicationEvents;

    use Brickoo\Core,
        Brickoo\Event,
        Brickoo\Module,
        Brickoo\Validator\TypeValidator;

    /**
     * This class is listening to the Brickoo\Core\Application events.
     * The cache, routing, session events are NOT implemented within this class.
     * @author Celestino Diaz <celestino.diaz@gmx.de>
     */

    class Application implements Event\Interfaces\ListenerAggregateInterface
    {

        /**
        * Holds the class dependencies.
        * @var array
        */
        protected $dependencies;

        /**
         * Returns the dependency holded, created or overwritten.
         * @param string $name the name of the dependency
         * @param string $interface the interface which has to be implemented by the dependency
         * @param callback $callback the callback to create a new dependency
         * @param object $Dependency the dependecy to inject
         * @return object Application if overwritten otherwise the dependency
         */
        protected function getDependency($name, $interface, $callback, $Dependency = null)
        {
            if ($Dependency instanceof $interface) {
                $this->dependencies[$name] = $Dependency;
                return $this;
            }
            elseif ((! isset($this->dependencies[$name])) || (! $this->dependencies[$name] instanceof $interface)) {
                $this->dependencies[$name] = call_user_func($callback, $this);
            }
            return $this->dependencies[$name];
        }

        /**
         * Lazy initialization of the Response dependency.
         * @param \Brickoo\Core\Interfaces\ResponseInterface $Response the Response dependency to inject
         * @return \Brickoo\Core\Interfaces\ResponseInterface
         */
        public function Response(\Brickoo\Core\Interfaces\ResponseInterface $Response = null)
        {
            return $this->getDependency(
                'Response',
                '\Brickoo\Core\Interfaces\ResponseInterface',
                function() {return new Response();},
                $Response
            );
        }

        /**
         * Holds an flag for preventing duplicate listener aggregation.
         * @var boolean
         */
        protected $listenerAggregated;

        /**
         * Registers the listeners to the EventManager.
         * This method is automaticly called by Brickoo\Core\Application::run if injected
         * since this application implements the ListenerAggreagteInterface.
         * @param \Brickoo\Event\Interfaces\EventManagerInterface $EventManager
         * @return void
         */
        public function aggregateListeners(\Brickoo\Event\Interfaces\EventManagerInterface $EventManager)
        {
            if ($this->listenerAggregated !== true) {
                $EventManager->attachListener(
                    Core\ApplicationEvents::EVENT_RESPONSE_GET, array($this, 'run')
                );
                $EventManager->attachListener(
                    Core\ApplicationEvents::EVENT_RESPONSE_SEND, array($this, 'sendResponse'), 0, array('Response')
                );
                $EventManager->attachListener(
                    Core\ApplicationEvents::EVENT_ERROR, array($this, 'displayError'), 0, array('Exception')
                );

                $this->listenerAggregated = true;
            }
        }

        /**
         * Sends a simple http response if an exception is throwed by the router
         * or within the Brickoo\Core\Application::run method.
         * This is just a dummy to display SOMETHING on errors.
         * @param \Exception $Exception the Exception throwed
         * @return void
         */
        public function displayError(\Exception $Exception)
        {
            $this->Response()->setContent("<html><head><title></title></head><body>\r\n".
                "<h1>This is not the response you are looking for...</h1>\r\n".
                "<div>(<b>Exception</b>: ". $Exception->getMessage() .")\r\n".
                "</body></html>"
            );
            $this->Response()->send();
        }

        /**
         * Returns always a fresh response.
         * Notifies the module boot event listeners.
         * @param \Brickoo\Event\Interfaces\EventInterface $Event the application event asking
         * @return \Brickoo\Core\Interfaces\ResponseInterface
         */
        public function run(\Brickoo\Event\Interfaces\EventInterface $Event)
        {
            if (($RequestRoute = $Event->getParam('Route')) instanceof \Brickoo\Routing\Interfaces\RequestRouteInterface) {

                $Response = null;

                try {
                    $RouteController = $RequestRoute->getModuleRoute()->getController();
                    if (! $RouteController['static']) {
                        $RouteController['controller'] = new $RouteController['controller'];
                    }

                    $Event->EventManager()->notify(new Event\Event(
                        Module\Events::EVENT_MODULE_BOOT, $Event->Sender(), array(
                            'controller' => $RouteController['controller'],
                            'method'     => $RouteController['method']
                        )
                    ));

                    $Response = $RouteController['controller']->$RouteController['method']($Event->Sender());

                    $Event->EventManager()->notify(new Event\Event(Module\Events::EVENT_MODULE_SHUTDOWN, $Event->Sender()));
                }
                catch (\Exception $Exception) {
                    $Event->EventManager()->notify(new Event\Event(
                        Module\Events::EVENT_MODULE_ERROR, $Event->Sender(), array('Exception' => $Exception)
                    ));
                }

                return $Response;
            }
        }

        /**
         * Sends the Response headers and content.
         * @param \Brickoo\Core\Interfaces\ResponseInterface $Response the request response
         * @return \Brickoo\Core\Application
         */
        public function sendResponse(\Brickoo\Core\Interfaces\ResponseInterface $Response)
        {
            $Response->send();

            return $this;
        }

    }