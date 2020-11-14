<?php

class Validator {
  private $data;
  private $errors = [];
  protected $fields = [];

  function __construct($postData) {
    $this->data = $postData; 
  }

  private function validate($field, $regex, $message) {
    $value = trim($this->data[$field]);
    
    if (empty($value)) {
      $this->pushError($field, "$field cannot be empty.");
    } elseif (!preg_match($regex, $value)) {
      $this->pushError($field, $message);
    }
  }

  private function pushError($field, $errMes) {
    $this->errors[$field] = $errMes; 
  }

  function validateForm () {
    foreach ($this->fields as $key => $value) {
      if (!array_key_exists($key, $this->data)) {
        trigger_error("$key is not present in data");
        return;
      }
    }

    foreach ($this->fields as $key => $value) {
      $this->validate($key, $value['regex'], $value['message']);
    }

    return $this->errors;
  }

  function setFields($fields) {
    $this->fields = $fields;
  }

  function addFields($fields) {
    $this->fields = array_merge($this->fields, $fields);
  }
}


class LoginValidator extends Validator {
  protected $fields = [
    'username' => [
      'regex' => '/^.+$/',
      'message' => 'username can not be empty'
    ], 
    'password' => [
      'regex' => '/^.+$/',
      'message' => 'password can not be empty'
    ]
  ];
}

class ManagerValidator extends Validator {
  protected $fields = [
    'fullname' => [
      'regex' => '/^[A-Za-z ]{1,50}$/',
      'message' => 'fullname must be alphabetic (a-z or A-Z).'
    ], 
    'username' => [
      'regex' => '/^[A-Za-z0-9]{4,20}$/',
      'message' => 'password must be 4-20 characters and alphanumeric.'
    ], 
    'password' => [
      'regex' => '/^[A-Za-z0-9]{4,20}$/',
      'message' => 'password must be 4-20 characters and alphanumeric.'
    ],
    'email' => [
      'regex' => '/^\w+@\w+\.\w+$/',
      'message' => 'please enter a valid email (e.g., John_Doe@gmail.com)'
    ] 
  ];
}

class HomeSlideshowValidator extends Validator {
  protected $fields = [
    'title' => [
      'regex' => '/^.+$/',
      'message' => 'title can not be empty'
    ], 
    'caption' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'caption can not be empty'
    ],
    'order' => [
      'regex' => '/^\d+|(auto)$/',
      'message' => 'Order must be a positive number or "auto"'
    ]
  ];
}

class HomeIntroductionValidator extends Validator {
  protected $fields = [
    'title' => [
      'regex' => '/^.+$/',
      'message' => 'title can not be empty'
    ], 
    'subtitle' => [
      'regex' => '/^.+$/',
      'message' => 'subtitle can not be empty'
    ],
    'content' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'Content can not be empty'
    ]
  ];
}

class ServiceValidator extends Validator {
  protected $fields = [
    'title' => [
      'regex' => '/^.+$/',
      'message' => 'title can not be empty'
    ], 
    'subtitle' => [
      'regex' => '/^.+$/',
      'message' => 'subtitle can not be empty'
    ],
    'content' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'Content can not be empty'
    ]
  ];
}

class TeamMemberValidator extends Validator {
  protected $fields = [
    'fullname' => [
      'regex' => '/^[A-Za-z ]{1,50}$/',
      'message' => 'fullname must be alphabetic (a-z or A-Z).'
    ], 
    'role' => [
      'regex' => '/^.+$/',
      'message' => 'role can not be empty'
    ],
    'description' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'description can not be empty'
    ],
    'facebook' => [
      'regex' => '/^[A-Za-z0-9\-\.\:\/\_]*facebook.com[A-Za-z0-9\-\.\:\/\_]*$/',
      'message' => 'please enter a valid facebook url'
    ],
    'twitter' => [
      'regex' => '/^[A-Za-z0-9\-\.\:\/\_]*twitter.com[A-Za-z0-9\-\.\:\/\_]*$/',
      'message' => 'please enter a valid twitter url'
    ],
    'linkedin' => [
      'regex' => '/^[A-Za-z0-9\-\.\:\/\_]*linkedin.com[A-Za-z0-9\-\.\:\/\_]*$/',
      'message' => 'please enter a valid linkedin url'
    ],
  ];
}

class EventValidator extends Validator {
  protected $fields = [
    'title' => [
      'regex' => '/^.+$/',
      'message' => 'title can not be empty'
    ], 
    'subtitle' => [
      'regex' => '/^.+$/',
      'message' => 'subtitle can not be empty'
    ],
    'description' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'Description can not be empty'
    ],
    'event_date' => [
      'regex' => '/^[0-9]{2,4}[\/-][0-9]{1,2}[\/-][0-9]{1,2}$/',
      'message' => 'Please enter a valid date (dd/mm/yyyy)'
    ]
  ];
}

class UserValidator extends Validator {
  protected $fields = [
    'username' => [
      'regex' => '/^[A-Za-z0-9]{4,20}$/',
      'message' => 'password must be 4-20 characters and alphanumeric.'
    ], 
    'password' => [
      'regex' => '/^[A-Za-z0-9]{4,20}$/',
      'message' => 'password must be 4-20 characters and alphanumeric.'
    ],
    'confirmPassword' => [
      'regex' => '/^[\s\S]+$/',
      'message' => 'Please confirm password'
    ],
    'email' => [
      'regex' => '/^\w+@\w+\.\w+$/',
      'message' => 'please enter a valid email (e.g., John_Doe@gmail.com)'
    ] 
  ];
}

class ClientValidator extends Validator {
  protected $fields = [
    'fullname' => [
      'regex' => '/^[A-Za-z ]{1,50}$/',
      'message' => 'lastname must be alphabetic (a-z or A-Z).'
    ], 
    'phone' => [
      'regex' => '/^[0-9]{8,16}$/',
      'message' => 'phone must be numeric (8-16 numbers)'
    ], 
    'email' => [
      'regex' => '/^\w+@\w+\.\w+$/',
      'message' => 'please enter a valid email (e.g., John_Doe@gmail.com)'
    ]
  ];
}