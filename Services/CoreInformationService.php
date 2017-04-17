<?php

namespace JsBundle\Services;

class CoreInformationService {

    const VERSION = "0.1.07";
    const RELEASE = "28/03/2017";
    
    public function __construct() {
        
    }
    
    /**
     * @return string
     */
    public function version(): string {
        return self::VERSION;
    }
    
    /**
     * @return string
     */
    public function release(): string {
        return self::RELEASE;
    }

    /**
     * @return string
     */
    public function symfonyVersion(): string {
        return \Symfony\Component\HttpKernel\Kernel::VERSION;
    }

    /**
     * @param int $id
     * @return string
     */
    public function certifiction(int $id): string {
        switch ($id) {
            case 1:
                return "[valid] Asserts";
            case 2:
                return "[valid] Asserts , Entity Doctrine";
            case 3:
                return "[valid] Asserts , Entity Doctrine, Roles";
            case 4:
                return "[valid] Asserts , Service Route & Roles  ";
            case 5:
                return "[valid] Asserts , Entity Doctrine , Service Route";
            case 6:
                return "[valid] Asserts , Entity Doctrine , Service Route, Service Roles";
        }
    }
    
    /**
     * @return string
     */
    public function date(): string {
        return time();
    }

}
