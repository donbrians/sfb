<?php
/**
 * Created by IntelliJ IDEA.
 * User: Akankwasa Brian
 * Date: 1/19/2022
 * Time: 1:10 PM
 */

class ApplicationLogicController
{

    /**
     * @var PDO|null
     */
    private $db;

    /**
     * ApplicationLogicController constructor.
     */
    public function __construct()
    {
        $this->db = (new DatabaseConnection())->getConnection();
    }


    /**
     * @param Request $payload
     * @return Response
     */
    public function generatePromoCodes(Request $payload)
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $batch = date("Ymdhms");
        $amount = $payload->getAmount();
        $radius = $payload->getRadius();

        if ($payload->getNumberOfCodes() < 1) {
            return new Response(500, "Number of Codes to generate cannot be less than 1");

        } elseif ($payload->getAmount() <= 0) {
            return new Response(500, "Amount for each generated code cannot be 0 or less ");

        } else {
            try {

                $this->db->beginTransaction();
                $this->db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

                for ($i = 0; $i < $payload->getNumberOfCodes(); $i++) {
                    $newCode = substr(str_shuffle($chars), 0, 5);
                    $query = " INSERT INTO generated_codes (code, amount, radius,batch_number) VALUES (:code, :amount, :radius, :batch_number);";

                    $statement = $this->db->prepare($query);
                    $statement->bindParam(":code", $newCode);
                    $statement->bindParam(":amount", $amount);
                    $statement->bindParam(":radius", $radius);
                    $statement->bindParam(":batch_number", $batch);

                    $executionResult=false;
                    if($statement->execute()){
                        $executionResult=true;
                    }
                }

                $this->db->commit();
                return new Response(0, $payload->getNumberOfCodes() . " Promo Codes each worth {$payload->getAmount()} Codes have been generated");


            } catch (PDOException $exception) {
                return new Response(500, $exception->getMessage());

            }
        }
    }

    /**
     * @param Request $payload
     * @return Response
     */
    public function deactivatePromoCode(Request $payload)
    {
        try{
            $query = " update  generated_codes set active='0' where code='".$payload->getCode()."' ;";
            $executionResult=false;
            if($this->db->query($query)){
                $executionResult=true;
            }

            return new Response($executionResult==true?0:500,$executionResult==true?"Operation was successful":"Operation Failed");

        }catch (PDOException $exception){
            return new Response(500, "An internal Server Error Occurred");
        }
    }

    /**
     * @param Request $payload
     * @return Response
     */
    public function listActivePromoCodes(Request $payload)
    {

        try{
            $query = " select * from generated_codes where active='1' ;";
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return new Response(0,count($result)." results found",$result);

        }catch (PDOException $exception){
            return new Response(500, "An internal Server Error Occured");
        }

    }

    /**
     * @param Request $payload
     * @return Response
     */
    public function listAllPromoCodes(Request $payload)
    {

        try{
            $query = " select * from generated_codes ;";
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return new Response(0,count($result)." results found",$result);

        }catch (PDOException $exception){
            return new Response(500, "An internal Server Error Occured");
        }

    }

    /**
     * @param Request $payload
     * @return Response
     */
    public function validatePromoCode(Request $payload)
    {
        try{
            $query = " select * from generated_codes where code='".$payload->getCode()."';";
            $statement = $this->db->query($query);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

            if(count($result)==0){
                return new Response(404,"Code {$payload->getCode()} was not found");

            }else{
                if($result[0]['active']<>1){
                    return new Response(403,$payload->getCode()." is no longer active");

                }elseif($result[0]['redeemed']<>0){
                    return new Response(400,$payload->getCode()." is already redeemed");

                }else{
                    return new Response(0,$payload->getCode()." is active and Ready to Use",$result[0]);

                }
            }

        }catch (PDOException $exception){
            return new Response(500, "An internal Server Error Occured");
        }
    }

    /**
     * @param Request $payload
     * @return Response
     */
    public function redeemCode(Request $payload){
        $responseCode=$this->validatePromoCode($payload)->getCode();

        if($responseCode<>0){
            return new Response(400,$payload->getCode()." is not active  to Use");
        }else{
            $query = " update  generated_codes set customer_number='".$payload->getPhoneNumber()."', redeemed='1', redeem_date='".date('YY:mm:dd h:i:s')."', active='0' where code='".$payload->getCode()."';";

            $executionResult=false;
            if($this->db->query($query)){
                $executionResult=true;
            }

            return new Response($executionResult==true?0:500,$executionResult==true?"Operation was successful":"Operation Failed");

        }
    }

}
