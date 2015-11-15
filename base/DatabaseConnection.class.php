<?php
/*
Copyright (c) 2015, André van Schoubroeck 
All rights reserved. 

Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are met: 

 * Redistributions of source code must retain the above copyright notice, 
   this list of conditions and the following disclaimer. 
 * Redistributions in binary form must reproduce the above copyright 
   notice, this list of conditions and the following disclaimer in the 
   documentation and/or other materials provided with the distribution. 
 * Neither the name of The IT Philosopher nor the names of its contributors 
   may be used to endorse or promote products derived from this software 
   without specific prior written permission. 

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
POSSIBILITY OF SUCH DAMAGE. 
*/

namespace Philosopher;

class DatabaseConnection extends Component{
/*
  Note: This is an initial implementation. Main development happens on 
        MySQL/MariaDB databases. Connection string for other database systems
        are provided for future development and may be incorrect.
  Note: Currently we do not have support for databases on custom ports. 
        Should this be supported?
  Note: This can be generalised by setting $dsn, $username, $password, $options
        in the switch/case and just write one new PDO() line.
*/

  function connect(){
    //NOTE: For now we're connecting using database information in constants
    //      We should implement a possibility to specify variables as well,
    //      To allow multiple database connections simultanously. This will
    //      enable us to run comparisons and also assists in database migration.
    //NOTE: Do we also need an option to specify custom? 
    if (defined("DBTYPE")) {
      switch (DBTYPE) {
        case 'MYSQL':
        case 'MARIADB':
          if (!(defined("DBHOST"))) throw new \Exception("DBHOST not defined");
          if (!(defined("DBNAME"))) throw new \Exception("DBNAME not defined");
          if (!(defined("DBUSER"))) throw new \Exception("DBUSER not defined");
          if (!(defined("DBPASS"))) throw new \Exception("DBPASS not defined");

           if (-1==version_compare(phpversion(),"5.3.6")) {
             $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
          }            else $options=null;
          $pdo = new \PDO('mysql:host='.DBHOST.
                               ';dbname='.DBNAME.
                               ';charset=utf8', DBUSER, DBPASS, $options);
          break;
        case 'PGSQL':
        case 'POSTGRES':
          if (!(defined("DBHOST"))) throw new \Exception("DBHOST not defined");
          if (!(defined("DBNAME"))) throw new \Exception("DBNAME not defined");
          if (!(defined("DBUSER"))) throw new \Exception("DBUSER not defined");
          if (!(defined("DBPASS"))) throw new \Exception("DBPASS not defined");

          $pdo = new \PDO('pgsql:host='.DBHOST.
                                ';dbname='.DBNAME, DBUSER, DBPASS);
          break;
        case 'SQLITE':
        case 'SQLITE3':
          if (!(defined("DBNAME"))) throw new \Exception("DBNAME not defined");
          $pdo = new \PDO('sqlite:'.DBNAME);
          break;
        case 'FIREBIRD':
        case 'INTERBASE':
          if (!(defined("DBNAME"))) throw new \Exception("DBNAME not defined");
          if (!(defined("DBUSER"))) throw new \Exception("DBUSER not defined");
          if (!(defined("DBPASS"))) throw new \Exception("DBPASS not defined");
          //can firebird run on a different server as well? how to specify?
          $pdo = new \PDO('firebird:dbname='.DBNAME, DBUSER, DBPASS);

        default:
          throw new \Exception("Unknown DBTYPE defined");
      }
      return $pdo; 
    } else {
      throw new \Exception("DBTYPE not defined");
    }
    
  }
    
//------------------------------------------------------------------------------
  public function getPDO() {
    return $pdo;
  }
}


?>


