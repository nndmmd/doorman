<?php

/***********************************************
  Authors : Huang Hao  2016.3.29 Kunming
  Doorman : roles and permissions chec
  Config files example:

permissions.cfg.php:

return [
  'login'       =>'/login',
  'app.home'    =>'/appstore',
  'app.detail'  =>'/appdetail',
  'admin.test'  =>'/test',
];

roles.cfg.php:

return [
  'admin'=>array(
    'login',    
    'admin.*',  
    'app.*',
  ),
  'operator'=>array(
    'login',
    'app.*',
  ),
  'anonymous'=>array(
    'login',
  ),
];

    Usage:

$doorman = new \Nndmmd\Doorman\Doorman;
$doorman->setRoles(require BASE_PATH.'/config/roles.cfg.php');
$doorman->setPermissions(require BASE_PATH.'/config/permissions.cfg.php');

if(!$doorman->can(['anonymous','operator'],'public/test',$roles)) {
    echo '拒绝访问<br/>';
    var_dump($roles);
    exit();
}


****************************/

    namespace Nndmmd\Doorman;

    class Doorman {
        private $roles;
        private $permissions;

        public function __construct() {
            $roles=array();
            $permissions=array();
        }

        public function __destruct() {
        }

        public function setPermissions($_permissions) {

            $this->permissions=array();
            $base_path=dirname($_SERVER['PHP_SELF']);

            foreach($_permissions as $key=>$value) {
                $this->permissions[$key]=$base_path.$value;
            }
        }

        public function setRoles($_roles) {
            $this->roles=$_roles;
        }

        private function matchPermission($subject) {

            foreach($this->permissions as $key=>$value) {
                if(preg_match('/'.str_replace('/','\/',$value).'/',$subject)) {
                    return $key;
                }
            }

            return null;
        }

        private function matchRole($subject) {
            $result=array();

            foreach($this->roles as $key=>$permissions) {
                foreach($permissions as $value) {
                    if(preg_match('/'.str_replace('/','\/',$value).'/',$subject)) {
                        $result[]=$key;
                        break;
                    }
                }
            }

            return $result;
        }

        public function can($user_roles,$subject,&$roles) {
            // 如果找不到匹配项，就返回空的roles
            // 这时can返回false，可以根据roles是否为空来判断是can't还是not found
            $roles=array();

            $permission=$this->matchPermission($subject);
            if(null==$permission) {
                return false;
            }

            $roles=$this->matchRole($permission);
            if(empty($roles)) {
                return false;
            }

            foreach($user_roles as $value) {
                if(in_array($value,$roles,true)) {
                    return true;
                }
            }

            return false;

        }

    }

