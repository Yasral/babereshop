<?php
namespace MailPoetVendor;
if (!defined('ABSPATH')) exit;
class Swift_Events_ResponseEvent extends Swift_Events_EventObject
{
 private $valid;
 private $response;
 public function __construct(Swift_Transport $source, $response, $valid = \false)
 {
 parent::__construct($source);
 $this->response = $response;
 $this->valid = $valid;
 }
 public function getResponse()
 {
 return $this->response;
 }
 public function isValid()
 {
 return $this->valid;
 }
}
