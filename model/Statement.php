<?php
/**
 * Created by PhpStorm.
 * User: Piotr
 */


namespace library\PigFramework\model;

/**
 * Class Statement
 * @package library\Pig\model
 */
class Statement
{
    /**
     * @var Statement
     */
    public static $instance;

    /**
     * @var array
     */
    protected $statements;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Statement constructor.
     */
    private function __construct()
    {
        $this->session = Registry::getInstance()->session;
        $this->statements = $this->session->statements ?? [];
    }

    /**
     * @return Statement
     */
    public static function getInstance(): Statement
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $type
     * @param $message
     * @throws \Exception
     */
    public function pushStatement(string $type, $message)
    {
        if (is_string($message)) {
            $this->statements[$type][] = $message;
        } else if (is_array($message)) {
            $this->statements[$type] = array_merge($this->statements[$type] ?? [], $message);
        } else {
            throw new \Exception("Bad message type. Expected string or array.");
        }

        $this->session->statements = $this->statements;
    }

    /**
     * @param array $statements
     * @throws \Exception
     */
    public function pushStatements(array $statements)
    {
        foreach ($statements as $type => $message) {
            $this->pushStatement($type, $message);
        }

    }

    /**
     * @param string|null $type
     * @return array
     */
    public function popStatements(string $type = null): array
    {
        if (empty($type)) {
            $statements = $this->statements;
            $this->statements = [];
        } else {
            $statements = $this->statements[$type];
            $this->statements[$type] = [];
        }

        $this->session->statements = $this->statements;

        return $statements;
    }

    /**
     * @param string|null $type
     */
    public function resetStatements(string $type = null)
    {
        if (empty($type)) {
            $this->statements = [];
        } else {
            $this->statements[$type] = [];
        }

        $this->session->statements = $this->statements;
    }
}