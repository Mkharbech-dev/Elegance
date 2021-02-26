<?php
namespace App\Classe;



use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
  private $api_key = '961b34fc640963ea30a8fd23ab4e9118';
  private $api_key_secret= '4bda9ef3bc0077e09d92dd3663ed3f6b';

  public  function send($to_email,$to_name,$subject,$content)
  {
    $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
    $body = [
      'Messages' => [
        [
          'From' => [
            'Email' => "elegancecaftan75@gmail.com",
            'Name' => "Elégance"
          ],
          'To' => [
            [
              'Email' => $to_email,
              'Name' => $to_name
            ]
          ],
          'TemplateID' => 2496221,
          'TemplateLanguage' => true,
          'Subject' => $subject,
          'Variables' => [
              'content' => $content,
            ]
        ]
      ]
    ];
    $response = $mj->post(Resources::$Email, ['body' => $body]);
    $response->success() ;
  }
}