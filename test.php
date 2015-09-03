<?php
error_reporting(1);
$fields_string = '';
$fields = array(
    'Username' => 'nooruldeen@creoroom.com',
    'Password' => 'cr2592',

    'xmlStream' => '<?xml version="1.0" encoding="UTF-8"?><WSGET>
  <AccessRequest>
    <WSVersion>WS1.0</WSVersion>
    <FileType>5</FileType>
    <Action>getprice</Action>
    <EntityID>4C339A58063EF9C95B900BC69FBFECA2</EntityID>
    <EntityPIN>cr2592</EntityPIN>
    <MessageID>0001</MessageID>
    <CarrierID>ACC001</CarrierID>
    <AccountCode>CR2593</AccountCode>
    <CreatedDateTime></CreatedDateTime>
  </AccessRequest>
  <RateReq>
    <FromType>C</FromType>
    <FromName>Singapore</FromName>
    <DestinationType>C</DestinationType>
    <DestinationName>France</DestinationName>
    <Serv>EN</Serv>
    <CurrencyCode>USD</CurrencyCode>
    <Weight>100</Weight>
  </RateReq>
</WSGET>',

    'LevelConfirm' => 'summary'
);

 //url-ify the data for the POST

foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }

rtrim($fields_string,'&');

 //echo $fields_string;

//open connection

$ch = curl_init();



//set the url, number of POST vars, POST data
$url = "https://ws05.ffdx.net/ffdx_ws/v12/service_ffdx.asmx/WSDataTransfer";
curl_setopt($ch,CURLOPT_URL,$url);

curl_setopt($ch,CURLOPT_POST,count($fields));

curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);


// Send to remote and return data to caller.
  $result = curl_exec($ch);
  curl_close($ch);
  echo $result;

?>