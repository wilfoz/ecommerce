<?php 

	namespace Hcode\Model;
	use \Hcode\Mailer;
	use \Hcode\DB\Sql;
	use \Hcode\Model;

	Class User extends Model{

		const SESSION = "User";
		const SECRET  = "HcodePhp7_Secret";
		const ERROR   = "UserError";
		const ERROR_REGISTER = "UserErrorRegister";

		public static function getFromSession()
		{
			$user = new User();

			if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0) {
				
				$user->setData($_SESSION[User::SESSION]);
			}

			return $user;
		}

		public static function checkLogin($inadmin = true)
		{
			if (
				!isset($_SESSION[User::SESSION])
				||
				!$_SESSION[User::SESSION]
				||
				!(int)$_SESSION[User::SESSION]["iduser"] >0) {

				// Não esta logado
				return false;

			} else {

				if ($inadmin === true && (bool)$_SESSION[User::SESSION]["inadmin"] === true) 
				{

					return true;

				} else if ($inadmin === false) {
					
					return true;

				} else {

					return false;
				}
			}

		}

		public static function login($login, $password){

			$sql = new Sql();
			$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
				":LOGIN"=>$login
			));

			if (count($results) === 0) {
				throw new \Exception("Usuario inexistente ou senha inválida.");
			}

			$data = $results[0];

			if (password_verify($password, $data["despassword"]) === true) {

				$user = new User();

				$user->setData($data);

				$_SESSION[User::SESSION] = $user->getValues();

				return $user;


			}else {
				throw new \Exception("Usuario inexistente ou senha inválida.");
			}
		}

		public static function verifyLogin($inadmin = true)
		{
			if (!User::checkLogin($inadmin)){

				if ($inadmin) {

					header("Location: /adm/login");
				}

				} else {
					header("Location: /login");
				}

			exit;
		}

		public static function logout(){
			$_SESSION[User::SESSION] = NULL;
		}

		public static function listAll(){
			$sql = new Sql();

			return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
		}

		public function save(){
			$sql = new Sql();

			$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()
			));

			$this->setData($results[0]);
		}

		public function get($iduser){
			$sql = new Sql();
			$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
				":iduser"=>$iduser
			));

			$this->setData($results[0]);
		}

		public function update(){
			$sql = new Sql();

			$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
				":iduser"=>$this->getiduser(),
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()
			));

			$this->setData($results[0]);
		}

		public function delete(){
			$sql = new Sql();

			$sql->query("CALL sp_users_delete(:iduser)",array(
				":iduser"=>$this->getiduser()
			));
		}

		public static function getForgot($email){
			$sql = new Sql();

			$results = $sql->select("
				SELECT * FROM tb_persons a
				INNER JOIN tb_users b 
				USING(idperson)
				WHERE a.desemail = :email;
				",array(
					":email"=>$email
				));

			if (count($results) === 0) {
				throw new \Exception("Não foi possível recuperar a senha");
				
			} else {

				$data = $results[0];

				$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
					":iduser"=>$data["iduser"],
					":desip"=>$_SERVER["REMOTE_ADDR"] //pega o ip do usuario
				));

				if (count($results2) === 0) {
					throw new \Exception("Não foi possível recuperar a senha");
				}else {
					
					$dataRecovery = $results2[0];

					$code  = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

					$link = "http://www.hcodecommerce.com.br/adm/forgot/reset?code=$code";

					$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir email", "forgot", array(
						"name"=>$data["desperson"],
						"link"=>$link
					));

					$mailer->send();

					return $data;
				}
			}
		}

		public static function validForgotDecrypt($code){

			$idrecovery = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, User::SECRET, base64_decode($code), MCRYPT_MODE_ECB);

			$sql = new Sql();

			$results = $sql->select("
				SELECT * FROM tb_userspasswordsrecoveries AS a
				INNER JOIN tb_users AS b USING(iduser)
				INNER JOIN tb_persons AS c USING(idperson)
				WHERE a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				date_add(a.dtregister, interval 1 hour)>= now();
			", array(
				":idrecovery"=>$idrecovery
			));

			if (count($results) === 0) {
				throw new \Exception("Não foi possível recuperar a senha");
			} else {

				return $results[0];
			}

		}

		public static function setForgotUsed($idrecovery)
		{
			$sql = new Sql;

			$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
				":idrecovery"=>$idrecovery
			));
		}

		public function setPassword($password)
		{
			$sql = new Sql;

			$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(
				":password"=>$password,
				":iduser"=>$this->getiduser()
			));
		}

		public static function setError($msg)
		{

			$_SESSION[User::ERROR] = $msg;

		}

		public static function getError()
		{

			$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

			User::clearError();

		}

		public static function clearError()
		{

			$_SESSION[User::ERROR] = NULL;

		}

		public static function setErrorRegister()
		{

			$_SESSION[User::ERROR_REGISTER] = $msg;

		}
	}


?>