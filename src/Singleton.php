<?php

declare(strict_types = 1);

namespace SEOCLI;

abstract class Singleton
{
    /**
     * instance.
     *
     * Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
     *
     * @var Singleton
     */
    protected static $_instance = null;

    /**
     * constructor.
     *
     * externe Instanzierung verbieten
     */
    protected function __construct()
    {
    }

    /**
     * clone.
     *
     * Kopieren der Instanz von aussen ebenfalls verbieten
     */
    protected function __clone()
    {
    }

    /**
     * get instance.
     *
     * Falls die einzige Instanz noch nicht existiert, erstelle sie
     * Gebe die einzige Instanz dann zurück
     *
     * @return   Singleton
     */
    public static function getInstance()
    {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
}
