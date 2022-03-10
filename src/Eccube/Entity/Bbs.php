<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Bbs')) {
    /**
     * Bbs
     *
     * @ORM\Table(name="dtb_bbs")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\BbsRepository")
     */
    class Bbs extends \Eccube\Entity\AbstractEntity
    {
                
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;
        
        /**
         * @var string|null
         *
         * @ORM\Column(name="message", type="string", length=5000, nullable=true)
         */
        private $message;

        /**
         * @var string|null
         *
         * @ORM\Column(name="image", type="string", length=255, nullable=true)
         */
        private $image;
        
        /**
         * @var \DateTime
         *
         * @ORM\Column(name="create_date", type="datetimetz")
         */
        private $create_date;

        /**
         * @var \Eccube\Entity\Order
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Order")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="order_id", referencedColumnName="id")
         * })
         */
        private $Order;
        
        /**
         * @var \Eccube\Entity\Customer
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Customer")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
         * })
         */
        private $Customer;
        
        /**
         * @var \Eccube\Entity\Member
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
         * })
         */
        private $Creator;  

        /**
         * Get id.
         *
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set message.
         *
         * @param string $message
         *
         * @return Bbs
         */
        public function setMessage($message)
        {
            $this->message = $message;

            return $this;
        }

        /**
         * Get message.
         *
         * @return string
         */
        public function getMessage()
        {
            return $this->message;
        }
        
        /**
         * Set image.
         *
         * @param string $image
         *
         * @return Bbs
         */
        public function setImage($image)
        {
            $this->image = $image;
            
            return $this;
        }
        
        /**
         * Get image.
         *
         * @return string
         */
        public function getImage()
        {
            return $this->image;
        }
        
        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Bbs
         */
        public function setCreateDate($createDate)
        {
            $this->create_date = $createDate;
            
            return $this;
        }
        
        /**
         * Get createDate.
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set order.
         *
         * @param \Eccube\Entity\Order|null $order
         *
         * @return Bbs
         */
        public function setOrder(Order $order = null)
        {
            $this->Order = $order;

            return $this;
        }

        /**
         * Get order.
         *
         * @return \Eccube\Entity\Order|null
         */
        public function getOrder()
        {
            return $this->Order;
        }

        public function getOrderId()
        {
            if (is_object($this->getOrder())) {
                return $this->getOrder()->getId();
            }

            return null;
        }
        
        /**
         * Set customer.
         *
         * @param \Eccube\Entity\Customer|null $customer
         *
         * @return Bbs
         */
        public function setCustomer(Customer $customer = null)
        {
            $this->Customer = $customer;
            
            return $this;
        }
        
        /**
         * Get customer.
         *
         * @return \Eccube\Entity\Customer|null
         */
        public function getCustomer()
        {
            return $this->Customer;
        }
        
        /**
         * Set creator.
         *
         * @param \Eccube\Entity\Member|null $creator
         *
         * @return Bbs
         */
        public function setCreator(Member $creator = null)
        {
            $this->Creator = $creator;
            
            return $this;
        }
        
        /**
         * Get creator.
         *
         * @return \Eccube\Entity\Member|null
         */
        public function getCreator()
        {
            return $this->Creator;
        }

        
    }
}
