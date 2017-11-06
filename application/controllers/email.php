<?php defined('BASEPATH') OR exit('No direct script access allowed');  
 class Email extends CI_Controller { 
  public function send()  
  {  
  $contact = array();
  $allcontact = $this->check_email();
  // var_dump($allcontact['data'][0]->email);
  $i = 0;
  foreach ($allcontact['data'] as $e) {
    # code...
    $contact[$i] = $e->email;
    $i++;
  }
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: PUT, GET, POST");
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept"); 
   $config = Array(  
    'protocol' => 'smtp',  
    'smtp_host' => 'ssl://smtp.googlemail.com',  
    'smtp_port' => 465,  
    'smtp_user' => 'saskiamalia13@gmail.com',   
    'smtp_pass' => '13091999saski',   
    'mailtype' => 'html',   
    'charset' => 'iso-8859-1'  
   );  
   $this->load->library('email', $config);  
   $this->email->set_newline("\r\n");  
   $this->email->from('recodeku@gmail.com', 'Admin Re:Code');   
   $this->email->to($contact); 
   $subject = $this->input->post('subject');
   $content = $this->input->post('content');
   $id = $this->input->post('id');
    if(is_null($subject)) {
    $result['status'] = 0;
    $result['message'] = "Subject must be provided";
    echo json_encode($result);   
    exit;
   }
   if(is_null($content)) {
    $result['status'] = 0;
    $result['message'] = "Content must be provided";
    echo json_encode($result);   
    exit;
   } 
   if(is_null($id)) {
    $result['status'] = 0;
    $result['message'] = "Id must be provided";
    echo json_encode($result);   
    exit;
  }
   $this->email->subject($_POST['subject']);   
   $this->email->message($_POST['content']); 

   if (!$this->email->send()) {  
    // show_error($this->email->print_debugger());   
    $result['status'] = 0;
    $result['message'] = "Email send failed!";
    echo json_encode($result);   
   
   }else{  
    $this->db->set('status', 'sent');
    $this->db->where('id', $id);
    $this->db->update('email_schedule');

    $this->db->set('send_at', date('Y-m-d H:i:s'));
    $this->db->where('id', $id);
    $this->db->update('email_schedule');

    $result['status'] = 1;
    $result['message'] = "Email send success!";
    echo json_encode($result);   
   }  
  }  


  public function check()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    $this->db->where('scheduled_at <=', 'NOW()');
    $this->db->where('status =', 'waiting');
    $this->db->join('email_template', 'email_schedule.template_id = email_template.email_id');

    $result['status'] = 1;
    $result['data'] = $this->db->get('email_schedule')->result();
    echo json_encode($result);  
  }

  public function check_all()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    // $this->db->where('scheduled_at >=', date('Y-m-d H:i:s'));
    $this->db->where('status =', 'waiting');
    $result['status'] = 1;
    $result['data'] = $this->db->get('email_schedule')->result();
    echo json_encode($result);  
  }

  public function check_contact()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    $result['status'] = 1;
    $result['data'] = $this->db->get('email_contact')->result();
    echo json_encode($result);
  }

  public function check_email()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    $result['status'] = 1;
    $result['data'] = $this->db->get('email_contact')->result();
    return $result;
  }

  public function list_all()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    $result['data'] = $this->db->get('email_schedule')->result();
    echo json_encode($result);  

  }
  
 }  