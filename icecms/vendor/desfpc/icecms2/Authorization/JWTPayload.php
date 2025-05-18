<?php
declare(strict_types=1);
/**
 * iceCMS2 v0.1a
 * Created by Sergey Peshalov https://github.com/desfpc
 * https://github.com/desfpc/iceCMS2
 *
 * JWT token model class
 */

namespace iceCMS2\Authorization;

class JWTPayload
{
    /** @var string */
    private string $_iss;
    /** @var int */
    private int $_iat;
    /** @var int */
    private int $_exp;
    /** @var int */
    private int $_uid;
    /** @var string */
    private string $_type;

    /**
     * JWToken constructor
     *
     * @param string $iss
     * @param int $iat
     * @param int $exp
     * @param int $uid
     * @param string $type
     */
    public function __construct(string $iss, int $iat, int $exp, int $uid, string $type)
    {
        $this->_iss = $iss;
        $this->_iat = $iat;
        $this->_exp = $exp;
        $this->_uid = $uid;
        $this->_type = $type;
    }

    /**
     * Get token issuer
     *
     * @return string
     */
    public function getIss(): string
    {
        return $this->_iss;
    }

    /**
     * Get token issue time
     *
     * @return int
     */
    public function getIat(): int
    {
        return $this->_iat;
    }

    /**
     * Get token expiration time
     *
     * @return int
     */
    public function getExp(): int
    {
        return $this->_exp;
    }

    /**
     * Get token user ID
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->_uid;
    }

    /**
     * Get token type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }
}