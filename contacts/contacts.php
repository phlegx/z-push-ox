<?php

class OXContactSync {

  private $OXConnector;
  private $OXUtils;

  public $mappingContactsOXtoASYNC = array('strings' => array('file_as' => 'fileas', 'last_name' => 'lastname', 'first_name' => 'firstname', 'second_name' => 'middlename', 'nickname' => 'nickname', 'title' => 'title', 'department' => 'department', 'suffix' => 'suffix', 'anniversary' => 'anniversary', 'assistant_name' => 'assistantname', 'telephone_assistant' => 'assistnamephonenumber', 'spouse_name' => 'spouse', 'note' => 'body', 'instant_messenger1' => 'imaddress', 'instant_messenger2' => 'imaddress2', 'city_home' => 'homecity', 'country_home' => 'homecountry', 'postal_code_home' => 'homepostalcode', 'state_home' => 'homestate', 'street_home' => 'homestreet', 'city_business' => 'businesscity', 'country_business' => 'businesscountry', 'postal_code_business' => 'businesspostalcode', 'state_business' => 'businessstate', 'street_business' => 'businessstreet', 'city_other' => 'othercity', 'country_other' => 'othercountry', 'postal_code_other' => 'otherpostalcode', 'state_other' => 'otherstate', 'street_other' => 'otherstreet', 'manager_name' => 'managername', 'email1' => 'email1address', 'email2' => 'email2address', 'email3' => 'email3address', 'company' => 'companyname', 'position' => 'jobtitle', 'url' => 'webpage', 'telephone_home1' => 'homephonenumber', 'telephone_home2' => 'home2phonenumber', 'cellular_telephone1' => 'mobilephonenumber', 'pagernumber' => 'telephone_pager', 'telephone_car' => 'carphonenumber', 'fax_home' => 'homefaxnumber', 'telephone_radio' => 'radiophonenumber', 'telephone_business1' => 'businessphonenumber', 'telephone_business2' => 'business2phonenumber', 'fax_business' => 'businessfaxnumber', 'categories' => 'categories',

  // String indicating the Japanese phonetic rendering
  // http://msdn.microsoft.com/en-us/library/office/aa221860(v=office.11).aspx
  'yomiFirstName' => 'yomifirstname', 'yomiLastName' => 'yomilastname', 'yomiCompany' => 'yomicompanyname', ), 'dates' => array('birthday' => 'birthday', ), 'datetimes' => array(), );

  public $mappingContactsASYNCtoOX = array();
  // will be filled after login

  public function OXContactSync($OXConnector, $OXUtils) {
    $this -> OXConnector = $OXConnector;
    $this -> OXUtils = $OXUtils;
    $this -> mappingContactsASYNCtoOX = $this -> OXUtils -> reversemap($this -> mappingContactsOXtoASYNC);
    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync initialized.');
  }

  public function GetMessageList($folder, $cutoffdate) {

    $folderid = $folder -> serverid;

    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync::GetMessageList(' . $folderid . ')  cutoffdate: ' . $cutoffdate);

    // handle contacts
    $response = $this -> OXConnector -> OXreqGET('/ajax/contacts', array('action' => 'all', 'session' => $this -> OXConnector -> getSession(), 'folder' => $folderid, 'columns' => '1,5,', //objectID�| last modified
    ));

    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync::GetMessageList(folderid: ' . $folderid . '  folder: ' . $folder -> displayname . '  data: ' . json_encode($response) . ')');

    $messages = array();
    foreach ($response["data"] as &$contact) {
      $message = array();
      $message["id"] = $contact[0];
      $message["mod"] = $contact[1];
      $message["flags"] = 1;
      // always 'read'
      $messages[] = $message;
    }

    return $messages;
  }

  public function GetMessage($folder, $id, $contentparameters) {

    $folderid = $folder -> serverid;

    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync::GetMessage(' . $folderid . ', ' . $id . ', ...)');

    $response = $this -> OXConnector -> OXreqGET('/ajax/contacts', array('action' => 'get', 'session' => $this -> OXConnector -> getSession(), 'id' => $id, 'folder' => $folderid, ));

    return $this -> OXUtils -> mapValues($response["data"], new SyncContact(), $this -> mappingContactsOXtoASYNC, 'php');

  }

  public function StatMessage($folder, $id) {
    $folderid = $folder -> serverid;

    // Default values:
    $message = array();
    $message["id"] = $id;
    $message["flags"] = 1;

    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync::StatMessage(' . $folderid . ', ' . $id . ', ...)');

    $response = $this -> OXConnector -> OXreqGET('/ajax/contacts', array('action' => 'get', 'session' => $this -> OXConnector -> getSession(), 'id' => $id, 'folder' => $folderid, ));
    $message["mod"] = $response["data"]["last_modified"];
    return $message;

  }

  /**
   * Changes the 'read' flag of a message on disk
   *
   * @param string        $folder       id of the folder
   * @param string        $id             id of the message
   * @param int           $flags          read flag of the message
   *
   * @access public
   * @return boolean                      status of the operation
   * @throws StatusException              could throw specific SYNC_STATUS_* exceptions
   */
  public function SetReadFlag($folder, $id, $flags) {
    ZLog::Write(LOGLEVEL_DEBUG, 'OXContactSync::SetReadFlag(' . $folderid . ', ' . $id . ', ' . $flags . ')');
  }

}
?>