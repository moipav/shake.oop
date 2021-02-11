<?php


namespace MyProject\Models;

use MyProject\Services\Db;

abstract class ActiveRecordEntity
{
    /** @var string int */
    protected $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function __set(string $name, $value)
    {
        // TODO: Implement __set() method.
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;

    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    /**
     * @return  static[]
     */
    public static function findAll(): array
    {
        $db = Db::getInstance();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);


    }

    //это позволит на не привязываться к конкретному классу или таблице
    abstract protected static function getTableName(): string;


    /**
     * @param int $id
     * @return static|null
     */

    public static function getById(int $id): ?self
    {
        $db = DB::getInstance();
        $entities = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE id=:id;',
            [':id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('/(?!^)[A-Z]/', '_$0', $source));
    }

    /*Но перед этим нам стоит обратить внимание, что метод save() может быть вызван как у объекта,
    который уже есть в базе данных, так и у нового (если мы создали его с помощью new Article и заполнили
    ему свойства)
    */
    public function save(): void
    {
        $mappedProperties = $this->mapPropertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }



    }

    private function update(array $mappedProperties): void
    {
        $columns2params = [];
        $params2values = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index;//:param1
            $columns2params[] = $column . ' = ' . $param;//column1 = :param1
            $params2values[$param] = $value;// [:param1=>value1]
            $index++;
        }
        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columns2params) . ' WHERE id = ' . $this->id.';';
        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);

    }

    protected function insert(array $mappedProperties): void
    {
        //удалим нулевые занчения из массива
        $filteredProperties = array_filter($mappedProperties);
        $columns = [];
        foreach ($filteredProperties as $columnName => $value) {
            //для начала сформируем массив, содержащий названия столбцов в таблице
            $columns[] = '`' . $columnName . '`';

            //А теперь подготовим массив с именами подстановок, вроде :author_id и :name.
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            //подготовим параметры, которые нужно будет подставить в запрос
            $params2values[$paramName] = $value;
        }
        //собираем готовый запрос
        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);

        $sql = 'INSERT INTO ' . static::getTableName() . ' (' . $columnsViaSemicolon . ') VALUES (' . $paramsNamesViaSemicolon . ');';

        $db = Db::getInstance();
        $db->query($sql, $params2values, static::class);
        //позволит избежать повторной записи т.к. $article->save id пустой
        $this->id = $db->getLastInsertId();

/**
 *
 * необходимо чтобы при добавлении пользвателя в обьект передавалась дата создания
 */


    }


    public function delete(): void
    {
        echo 'del';
        $db = Db::getInstance();
        $db->query(
            'DELETE FROM `' . static::getTableName() . '` WHERE id = :id', [':id' => $this->id]
        );

        $this->id = null;
    }

    private function mapPropertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properties = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }

        return $mappedProperties;
        /*Здесь мы получили все свойства, и затем каждое имяСвойства привели к имя_свойства. После чего в массив
        $mappedProperties мы стали добавлять элементы с ключами «имя_свойства» и со значениями этих свойств.*/
    }

    /*
        добавили protected-свойство ->id и public-геттер для него – у всех наших сущностей будет id, и нет необходимости писать это каждый раз в каждой сущности – можно просто унаследовать;
        перенесли public-метод __set() – теперь все дочерние сущности будут его иметь
        перенесли метод underscoreToCamelCase(), так как он используется внутри метода __set()
        public-метод findAll() будет доступен во всех классах-наследниках
        и, наконец, мы объявили абстрактный protected static метод getTableName(), который должен вернуть строку – имя таблицы. Так как метод абстрактный, то все сущности, которые будут наследоваться от этого класса, должны будут его реализовать. Благодаря этому мы не забудем его добавить в классах-наследниках.
    */

    /**
     * проверка дубликатов логин и пароль в БД
     */
    public static function findOneByColumn(string $columnName, $value): ?self
    {
        $db = Db::getInstance();
        $result = $db->query(
            'SELECT * FROM `' . static::getTableName() . '` WHERE `' .$columnName . '` = :value LIMIT 1;',
            [':value' => $value], static::class
        );
        if ($result ===[]){
            return null;
        }
        return $result[0];
    }
}