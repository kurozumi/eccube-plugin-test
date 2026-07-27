<?php

namespace Plugin\SamplePlugin44\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Plugin\SamplePlugin44\Entity\Config', false)) {
    /**
     * Config
     */
    #[ORM\Table(name: "plg_sample_plugin44_config")]
    #[ORM\Entity(repositoryClass: "Plugin\SamplePlugin44\Repository\ConfigRepository")]
    class Config
    {
        /**
         * @var int
         *
         */
        #[ORM\Id]
        #[ORM\Column(name: "id", type: "integer", options: ["unsigned" => true])]
        #[ORM\GeneratedValue(strategy: "IDENTITY")]
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: "name", type: "string", length: 255)]
        private $name;

        /**
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * @return string
         */
        public function getName()
        {
            return $this->name;
        }

        /**
         * @param string $name
         *
         * @return $this;
         */
        public function setName($name)
        {
            $this->name = $name;

            return $this;
        }
    }
}
