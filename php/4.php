<?php

// Challenge: make this terrible code safe

trait passwordEncryption
{

	public function encrypt_password($password)
	{
		return password_hash($password, PASSWORD_BCRYPT);
	}

	public function verify_password($password, $hash)
	{
		return password_verify($password, $hash);
	}

}

class DB
{

	use passwordEncryption;

	private $pdo;

	public function __construct()
	{
		$this->pdo = new PDO('sqlite::memory:');
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function truncateUsersTable()
	{
		$this->pdo->exec("DROP TABLE IF EXISTS users");
		$this->pdo->exec("CREATE TABLE users (username VARCHAR(255), password VARCHAR(255))");
	}

	public function addUser($name, $password)
	{
		$query = $this->pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password);");
		$query->bindValue(':username', $name, PDO::PARAM_STR);
		$query->bindValue(':password', $this->encrypt_password($password), PDO::PARAM_STR);
		$query->execute();
	}

	public function fetchUser($name)
	{
		$query = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
		$query->bindValue(':username', $name);
		$query->execute();

		return $query->fetchObject();
	}
}

class User
{

	protected $db;

	public function __construct(DB $db)
	{
		$this->db = $db;
	}

	public function removeAll()
	{
		$this->db->truncateUsersTable();
	}

	public function create($name, $password)
	{
		$this->db->addUser($name, $password);
	}

	public function exists($name, $password)
	{
		$user = $this->db->fetchUser($name);
		if ($user)
		{
			return $this->db->verify_password($password, $user->password);
		}
		return false;
	}
}


$username = @$_GET['username'] ? $_GET['username'] : 'Anna';
$password = @$_GET['password'] ? $_GET['password'] : 'Fleming';

$user = new User(new DB);
$user->removeAll();
$user->create('root', 'secret');


if ($user->exists($username, $password)) {
	echo "Access granted to $username!";
} else {
	echo "Access denied for $username!";
}
