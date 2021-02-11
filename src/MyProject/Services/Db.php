<?php


namespace MyProject\Services;


use MyProject\Exceptions\DbException;

class Db
{


    private static $instance;
    //**@var \PDO*/
    private $pdo;

    private function __construct()
    {

        $dbOptions = (require __DIR__ . '/../../settings.php')['db'];
        try {
            $this->pdo = new \PDO(
                'mysql:host=' . $dbOptions['host'] . ';dbname=' . $dbOptions['dbname'], $dbOptions['user'], $dbOptions['password']
            );
            $this->pdo->exec('SET NAMES UTF8');
        }catch (\PDOException $e){
            throw new DbException('Ошибка при подключении к БД: ' . $e->getMessage());
        }

    }



    /**
     * @return mixed
     */
    public static function getInstance():self
    {
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, $params = [], string $className = 'stdClass'): ? array
    {
        $sth = $this->pdo->prepare($sql);
        $result = $sth->execute($params);

        if (false === $result){
            return null;
        }
        return $sth->fetchAll(\PDO::FETCH_CLASS, $className);
        //В метод fetchAll() мы передали специальную
        // константу - \PDO::FETCH_CLASS, она говорит о том,
        // что нужно вернуть результат в виде объектов какого-то класса.
        // Второй аргумент – это имя класса, которое мы можем передать
        // в метод query().
    }

    /*методе save() мы проверяем значение поля id, и если оно равно null,
     то мы вызываем insert(), а не update(). Давайте исправим
    это недоразумение. Для того, чтобы получить id последней
    вставленной записи в базе (в рамках текущей сессии работы с БД)
    можно использовать метод lastInsertId() у объекта PDO.
     Давайте в нашем классе Db добавим следующий метод:*/

    public function getLastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    public function getDateLastInsert()
    {

    }


}