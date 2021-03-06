<?php

class User extends Db_object
{

    protected static $db_table = "users";
    protected static $db_table_fields = array('username', 'password', 'first_name', 'last_name', 'user_image');
    public $id;
    public $username;
    public $password;
    public $first_name;
    public $last_name;
    public $user_image;
    public $upload_directory = "images";
    public $image_placeholder = "http://placehold.it/400x400&text=image";



    protected function properties(){
        

        $properties = array();

        foreach (static::$db_table_fields as $db_field) {
            
            if(property_exists($this, $db_field)){

                 $properties[$db_field] = $this->$db_field;

            }

        }

        return $properties;
    }
    
    public function upload_photo()
    {
        if (!empty($this->errors)) {

            return false;
        }

        if (empty($this->user_image) || empty($this->tmp_path)) {

            $this->errors[] = "The file was not available.";
            return false;
        }

        $target_path = SITE_ROOT . DS . 'admin' . DS . $this->upload_directory . DS . $this->user_image;

        if (file_exists($target_path)) {

            $this->errors[] = "The file {$this->user_image} already exists.";
            return false;
        }

        if (move_uploaded_file($this->tmp_path, $target_path)) {

                unset($this->tmp_path);
                return true;
        } else {

            $this->errors[] = "The file directory probably does not have permission.";
            return false;
        }

        $this->create();
    } //save method end


    public function image_path_and_placeholder()
    {

        return empty($this->user_image) ? $this->image_placeholder : $this->upload_directory . DS . $this->user_image;
    }

    public static function verify_user($username, $password)
    {

        global $database;

        $username = $database->escape_string($username);
        $password = $database->escape_string($password);

        $sql = "SELECT * FROM " . self::$db_table . " WHERE ";
        $sql .= "username='{$username}' ";
        $sql .= "AND password='{$password}' ";
        $sql .= "LIMIT 1";

        $the_result_array = self::find_by_query($sql);

        return !empty($the_result_array) ? array_shift($the_result_array) : false;
    }






    ## EZ VALAMIÉRT NEM MŰKÖDÖTT DE HA KIFOLYIK A SZEMEM SE TUDOM MIÉRT
    // public function update(){  

    //     global $database;

    //     $sql    = "UPDATE users SET ";
    //     $sql   .= "username= '". $database->escape_string($this->username)     ."','";
    //     $sql   .= "password= '". $database->escape_string($this->password)     ."','";
    //     $sql   .= "first_name= '". $database->escape_string($this->first_name) ."','";
    //     $sql   .= "last_name= '". $database->escape_string($this->last_name)   ."' ";
    //     $sql   .= " WHERE id = ". $database->escape_string($this->id);

    //     $database->query($sql);

    //     return (mysqli_affected_rows($database->connection) == 1) ? true : false; 
    // }


} //End of Class user
