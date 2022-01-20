## Introduction to the Poject

this project is based on PHP7.0
it is not built on any framework, but it encompasses all the neccessary technologies to show OOP, function re-use


## Technology Stack Used
it uses mysql databases with the following structure


## Sample databse table structure
```mysql
CREATE TABLE `generated_codes` (
`id` INT NOT NULL AUTO_INCREMENT,
`code` VARCHAR ( 200 ) NOT NULL,
`amount` FLOAT NOT NULL,
`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
`batch_number` VARCHAR ( 200 ) NOT NULL,
`customer_number` VARCHAR ( 200 ) NOT NULL,
`redeemed` BOOLEAN NOT NULL DEFAULT FALSE,
`redeem_date` DATETIME NOT NULL,
`active` BOOLEAN NOT NULL DEFAULT TRUE,
`radius` FLOAT NOT NULL 
) ENGINE = INNODB;

```


## Application Entry Point
index.php

## URI Structure
http://localhost/safeboda/index.php/code-generation/generate

## URI Break Down
http://localhost -- host server
safeboda -- Context Path
index.php -- entry file
code-generation -- application context path, controller name
generate -- action to perform

## Running the project
Copy and paste the project files into a PHP based server with mysql installed on it, create a database called safe_boda_codes, run the sql script as shown in the database section and you are done.

## Classes needed to run the project
make sure you have the following includes in your index.php

```Php
include "api_responses/Response.php";
include "api_responses/Request.php";
include "controllers/ApplicationLogicController.php";
include "environment/LoadEnvironmentVariables.php";
include "database/DatabaseConnection.php";

```

## Request Structure

the application relies on Json Post for all the end points since it is supposed to be consumed as an API, the request structure takes upon the following struture

```phph

<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/19/2022
 * Time: 2:43 PM
 */

class Request
{
    private $code;
    private $phone_number;
    private $destination;
    private $origin;
    private $number_of_codes;
    private $amount;
    private $radius;

    public function set($key, $value){
        $this->{$key}=$value;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    /**
     * @param mixed $phone_number
     */
    public function setPhoneNumber($phone_number)
    {
        $this->phone_number = $phone_number;
    }

    /**
     * @return mixed
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param mixed $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return mixed
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param mixed $origin
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return mixed
     */
    public function getNumberOfCodes()
    {
        return $this->number_of_codes;
    }

    /**
     * @param mixed $number_of_codes
     */
    public function setNumberOfCodes($number_of_codes)
    {
        $this->number_of_codes = $number_of_codes;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getRadius()
    {
        return $this->radius;
    }

    /**
     * @param mixed $radius
     */ 
    public functionsetRadius($radius)
    {
        $this->radius = $radius;
    }


}

```

the JSON representation would be as shown below

```json
{
    "amount": "2000",
    "number_of_codes": 15,
    "radius": 100,
    "code": "XWMR3",
    "phone_number": "256772123022"
}

```

## Expected Response
```json

{
    "code": 0,
    "message": "Operation was successful",
    "data": null
}

```


## Expected Response Codes
All the response codes are standard HTTP Codes
