<?php

    //
    namespace Communications;

    //
    class Connection {
    
        /**
         * Connection
         * @var type 
         */
        private static $conn;
    
        /**
         * Connect to the database and return an instance of \PDO object
         * @return \PDO
         * @throws \Exception
         */
        public function connect() {

            // read parameters in the ini configuration file
            //$params = parse_ini_file('database.ini');
            $db = parse_url(getenv("DATABASE_URL"));

            //if ($params === false) {throw new \Exception("Error reading database configuration file");}
            if ($db === false) {throw new \Exception("Error reading database configuration file");}
            // connect to the postgresql database
            $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                    $db['host'],
                    $db['port'], 
                    ltrim($db["path"], "/"), 
                    $db['user'], 
                    $db['pass']);
    
            $pdo = new \PDO($conStr);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
            return $pdo;
        }
    
        /**
         * return an instance of the Connection object
         * @return type
         */
        public static function get() {
            if (null === static::$conn) {
                static::$conn = new static();
            }
    
            return static::$conn;
        }
    
        protected function __construct() {
            
        }
    
        private function __clone() {
            
        }
    
        private function __wakeup() {
            
        }
    
    }

    //
    class Token {

        /**
         * PDO object
         * @var \PDO
         */
        private $pdo;
    
        /**
         * init the object with a \PDO object
         * @param type $pdo
         */
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        /**
         * Return all rows in the stocks table
         * @return array
         */
        public function all() {
            $stmt = $this->pdo->query('SELECT id, symbol, company '
                    . 'FROM stocks '
                    . 'ORDER BY symbol');
            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['id'],
                    'symbol' => $row['symbol'],
                    'company' => $row['company']
                ];
            }
            return $stocks;
        }

        //
        public function validatedToken() {
            
            //
            return true;
            
            //exit;

        }

        //
        public function process_id($object='prc') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);

            $id = $object.'_'.$id;

            //
            return $id;
            
            //exit;

        }
        
        //
        public function event_id($object='evt') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);

            $id = $object.'_'.$id;
    
            //
            return $id;
            
            //exit;

        }

        //
        public function new_id($object='obj') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);
            $id = $object . "_" . $id;
    
            //
            return $id;
            
            //exit;

        }

        /**
         * Find stock by id
         * @param int $id
         * @return a stock object
         */
        public function check($id) {

            //
            $sql = "SELECT message_id FROM messages WHERE id = :id AND active = 1";

            // prepare SELECT statement
            $statement = $this->pdo->prepare($sql);
            // bind value to the :id parameter
            $statement->bindValue(':id', $id);
            
            // execute the statement
            $stmt->execute();
    
            // return the result set as an object
            return $stmt->fetchObject();
        }

        /**
         * Delete a row in the stocks table specified by id
         * @param int $id
         * @return the number row deleted
         */
        public function delete($id) {
            $sql = 'DELETE FROM stocks WHERE id = :id';
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
    
            $stmt->execute();
    
            return $stmt->rowCount();
        }

        /**
         * Delete all rows in the stocks table
         * @return int the number of rows deleted
         */
        public function deleteAll() {
    
            $stmt = $this->pdo->prepare('DELETE FROM stocks');
            $stmt->execute();
            return $stmt->rowCount();
        }

    }

    //
    class Notification {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Communications\Token($this->pdo);

        }

        //
        public function insertNotification($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('ntf');}

            $columns = "";

            // INSERT OBJECT - COLUMNS
            if(isset($request['id'])){$columns.="notification_id,";}
            if(isset($request['attributes'])){$columns.="notification_attributes,";}
            if(isset($request['message'])){$columns.="notification_message,";}
            if(isset($request['type'])){$columns.="notification_type,";}
            if(isset($request['opened'])){$columns.="notification_opened,";}
            if(isset($request['viewed'])){$columns.="notification_viewed,";}
            if(isset($request['recipient'])){$columns.="notification_recipient,";}
            if(isset($request['sender'])){$columns.="notification_sender,";}
            if(isset($request['subject'])){$columns.="notification_subject,";}
            if(isset($request['object'])){$columns.="notification_object,";}
            if(isset($request['profile'])){$columns.="profile_id,";}

            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";

            // INSERT OBJECT - VALUES
            if(isset($request['id'])){$values.=":notification_id,";}
            if(isset($request['attributes'])){$values.=":notification_attributes,";}
            if(isset($request['message'])){$values.=":notification_message,";}
            if(isset($request['type'])){$values.=":notification_type,";}
            if(isset($request['opened'])){$values.=":notification_opened,";}
            if(isset($request['viewed'])){$values.=":notification_viewed,";}
            if(isset($request['recipient'])){$values.=":notification_recipient,";}
            if(isset($request['sender'])){$values.=":notification_sender,";}
            if(isset($request['subject'])){$values.=":notification_subject,";}
            if(isset($request['object'])){$values.=":notification_object,";}
            if(isset($request['profile'])){$values.=":profile_id,";}

            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";

            //echo $sql;
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES
            if(isset($request['id'])){$statement->bindValue('notification_id',$request['id']);}
            if(isset($request['attributes'])){$statement->bindValue('notification_attributes',$request['attributes']);}
            if(isset($request['message'])){$statement->bindValue('notification_message',$request['message']);}
            if(isset($request['type'])){$statement->bindValue('notification_type',$request['type']);}
            if(isset($request['opened'])){$statement->bindValue('notification_opened',$request['opened']);}
            if(isset($request['viewed'])){$statement->bindValue('notification_viewed',$request['viewed']);}
            if(isset($request['recipient'])){$statement->bindValue('notification_recipient',$request['recipient']);}
            if(isset($request['sender'])){$statement->bindValue('notification_sender',$request['sender']);}
            if(isset($request['subject'])){$statement->bindValue('notification_subject',$request['subject']);}
            if(isset($request['object'])){$statement->bindValue('notification_object',$request['object']);}
            if(isset($request['profile'])){$statement->bindValue('profile_id',$request['profile']);}
            
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['notification_id'];

            return $data;
        
        }

        //
        public function selectNotifications($request) {

            //echo json_encode($request); exit;

            //$token = new \Core\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                notification_ID,
                notification_attributes,
                notification_message,
                notification_type,
                notification_opened,
                notification_viewed,
                notification_recipient,
                notification_sender,
                notification_subject,
                notification_object,
                profile_ID,
                app_ID

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= ' WHERE ';
                    $conditions.= ' ' . $prefix . '_id = :id ';
                    $conditions.= ' AND active = 1 ';
                    $conditions.= ' ORDER BY time_finished DESC ';

                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID
                    //if(isset($request['attributes'])){$refinements.="notification_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}
                    if(isset($request['message'])){$refinements.="notification_message"." ILIKE "."'%".$request['message']."%' AND ";}
                    if(isset($request['type'])){$refinements.="notification_type"." = "."'".$request['type']."' AND ";}
                    if(isset($request['opened'])){$refinements.="notification_opened"." = "."'".$request['opened']."' AND ";}
                    if(isset($request['viewed'])){$refinements.="notification_viewed"." = "."'".$request['viewed']."' AND ";}
                    if(isset($request['recipient'])){$refinements.="notification_recipient"." = "."'".$request['recipient']."' AND ";}
                    if(isset($request['sender'])){$refinements.="notification_sender"." = "."'".$request['sender']."' AND ";}
                    if(isset($request['subject'])){$refinements.="notification_subject"." ILIKE "."'%".$request['subject']."%' AND ";}
                    if(isset($request['object'])){$refinements.="notification_object"." = "."'".$request['object']."' AND ";}
                    if(isset($request['profile'])){$refinements.="profile_id"." = "."'".$request['profile']."' AND ";}

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= ' AND app_id = \'' . $request['app'] . '\' ';
                    $conditions.= ' AND profile_id = \'' . $request['profile'] . '\' ';
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['notification_id'],
                            'attributes' => json_decode($row['notification_attributes']),
                            'message' => $row['notification_message'],
                            'type' => $row['notification_type'],
                            'opened' => $row['notification_opened'],
                            'viewed' => $row['notification_viewed'],
                            'recipient' => $row['notification_recipient'],
                            'sender' => $row['notification_sender'],
                            'subject' => ['notification_subject'],
                            'object' => $row['notification_object'],
                            'profile' => $row['profile_id'],
                            'app' => $row['app_id']

                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateNotification($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            // UPDATE OBJECT - SET
            // SKIP as ID won't be getting UPDATED
            if(isset($request['attributes'])){$set.= " notification_attributes = :notification_attributes ";}
            if(isset($request['message'])){$set.= " notification_message = :notification_message ";}
            if(isset($request['type'])){$set.= " notification_type = :notification_type ";}
            if(isset($request['opened'])){$set.= " notification_opened = :notification_opened ";}
            if(isset($request['viewed'])){$set.= " notification_viewed = :notification_viewed ";}
            if(isset($request['recipient'])){$set.= " notification_recipient = :notification_recipient ";}
            if(isset($request['sender'])){$set.= " notification_sender = :notification_sender ";}
            if(isset($request['subject'])){$set.= " notification_subject = :notification_subject ";}
            if(isset($request['object'])){$set.= " notification_object = :notification_object ";}

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // UPDATE OBJECT - BIND VALUES
            //if(isset($request['id'])){$statement->bindValue(':notification_id', $request['id']);}
            if(isset($request['attributes'])){$statement->bindValue(':notification_attributes', $request['attributes']);}
            if(isset($request['message'])){$statement->bindValue(':notification_message', $request['message']);}
            if(isset($request['type'])){$statement->bindValue(':notification_type', $request['type']);}
            if(isset($request['opened'])){$statement->bindValue(':notification_opened', $request['opened']);}
            if(isset($request['viewed'])){$statement->bindValue(':notification_viewed', $request['viewed']);}
            if(isset($request['recipient'])){$statement->bindValue(':notification_recipient', $request['recipient']);}
            if(isset($request['sender'])){$statement->bindValue(':notification_sender', $request['sender']);}
            if(isset($request['subject'])){$statement->bindValue(':notification_subject', $request['subject']);}
            if(isset($request['object'])){$statement->bindValue(':notification_object', $request['object']);}
            //if(isset($request['profile'])){$statement->bindValue(':profile_id', $request['profile']);}

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['notification_id'];

            // return generated id
            return $data;

        }

        //
        public function deleteNotification($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

?>