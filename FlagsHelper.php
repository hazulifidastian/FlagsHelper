<?php
/**
*   @author Hazuli Fidastian <hazulifidastian@gmail.com>
*   @package Helpers
*/


/**
* Membantu mendefinisikan, validasi, pengecekan flags
* 
*/
class FlagsHelper {

    /**
     * Separator
     * @var string
     */
    private $sep = '|';

    /**
     * Hanya menerima flags yang valid
     * @var array
     */
    private $valid_flags = array(
        'LOCKED',
        'UNLOCKED',
        'ORIGINAL',
        'REPLACEMENT',
        );

    /**
     * Flag-flag
     * 
     * @var string
     */
    private $flags = array();

    /**
     * 
     * @var Singleton
    */
    private static $instance;

    /**
     * Inisialisasi
     * 
     * @param string $flags [description]
     * @return instance
     */
    public static function flags( $flags='' )
    {
        self::$instance = new self( $flags );
        return self::$instance;
    }

    /**
     * Constructor with flags
     * 
     * @param string $flags
     */
    private function __construct( $flags = '' )
    {
        $this->extract( $flags );
    }

    /**
     * Magic method to filter flag parameter from method insert, delete, exists and update
     * @param string $name Method name
     * @param array $args Arguments
     * @return call a function | $this
     */
    public function __call( $name, $args )
    {
        $validate_flag = array( 'insert', 'delete', 'exists' );
        if ( method_exists($this, "_{$name}") )
        {
            if( in_array($name, $validate_flag) and $this->validate($args[0]) )
                return call_user_func_array(array($this, "_{$name}"), $args);
            else if( $name == 'update' and $this->validate($args[0]) and $this->validate($args[1]) )
                return call_user_func_array(array($this, "_{$name}"), $args);
        }
        
        return $this;
    }

    /**
     * Extract to array and validate flags
     * 
     * @param string $flags
     * @return void
     */
    private function extract( $flags )
    {
        $flags = explode( $this->sep, $flags );
        foreach( $flags as $fl )
            if( $this->validate($fl) )
                $this->flags[] = $fl;
        }

    /**
     * Validasi apakah flag sudah dideklarasikan
     * 
     * @param string $flag
     * @return boolean Tersedia/tidak
     */
    private function validate( $flag )
    {   
        return in_array( $flag, $this->valid_flags );
    }

    /**
     * Cek apakah flag ada didalam flags
     * 
     * @param string $flag
     * @return boolean
     */
    public function _exists( $flag )
    {
        return in_array( $flag, $this->flags );
    }

    /**
     * Tambah
     * 
     * @param string $flag
     * @return FlagsHelper
     */
    public function _insert( $flag )
    {
        if( !$this->exists( $flag ) )
        {
            array_push( $this->flags, $flag );
            $this->flags = array_unique( $this->flags, SORT_STRING );
        }
        
        return $this;
    }

    /**
     * Hapus
     * 
     * @param string $flag
     * @return FlagsHelper
     */
    public function _delete( $flag )
    {
        $this->flags = array_diff( $this->flags, array($flag) );
        return $this;
    }

    /**
     * Update
     * 
     * @param string $flag
     * @return FlagsHelper
     */
    public function _update( $from_flag, $to_flag )
    {
        $this->delete( $from_flag );
        $this->insert( $to_flag );
        return $this;
    }

    /**
     * Convert to string
     * 
     * @return string
     */
    public function to_str()
    {
        return implode( $this->sep, $this->flags );
    }

    /**
     * Get flags string
     * 
     * @return string
     */
    public function select()
    {
        return $this->to_str();
    }

    /**
     * Convert to array
     * 
     * @return array
     */
    public function to_array()
    {
        return $this->flags;
    }

    /**
     * Convert to json
     * @return json string
     */
    public function to_json()
    {
        return json_encode( $this->flags );
    }
}