<?php

namespace PhpOffice\PhpSpreadsheet\Calculation;

use Complex\Complex;
use Complex\Exception as ComplexException;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\Bessel;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\BitWise;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering\ConvertUOM;

class Engineering
{
    /**
     * EULER.
     */
    const EULER = 2.71828182845904523536;

    /**
     * parseComplex.
     *
     * Parses a complex number into its real and imaginary parts, and an I or J suffix
     *
     * @deprecated 2.0.0 No longer used by internal code. Please use the \Complex\Complex class instead
     *
     * @param string $complexNumber The complex number
     *
     * @return mixed[] Indexed on "real", "imaginary" and "suffix"
     */
    public static function parseComplex($complexNumber)
    {
        $complex = new Complex($complexNumber);

        return [
            'real' => $complex->getReal(),
            'imaginary' => $complex->getImaginary(),
            'suffix' => $complex->getSuffix(),
        ];
    }

    /**
     * Formats a number base string value with leading zeroes.
     *
     * @param string $xVal The "number" to pad
     * @param int $places The length that we want to pad this value
     *
     * @return string The padded "number"
     */
    private static function nbrConversionFormat($xVal, $places)
    {
        if ($places !== null) {
            if (is_numeric($places)) {
                $places = (int) $places;
            } else {
                return Functions::VALUE();
            }
            if ($places < 0) {
                return Functions::NAN();
            }
            if (strlen($xVal) <= $places) {
                return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
            }

            return Functions::NAN();
        }

        return substr($xVal, -10);
    }

    /**
     * BESSELI.
     *
     *    Returns the modified Bessel function In(x), which is equivalent to the Bessel function evaluated
     *        for purely imaginary arguments
     *
     *    Excel Function:
     *        BESSELI(x,ord)
     *
     * @Deprecated 2.0.0 Use the BESSELI() method in the Engineering\BesselI class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELI returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function.
     *                                If ord is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELI returns the #VALUE! error value.
     *                                If $ord < 0, BESSELI returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELI($x, $ord)
    {
        return Engineering\BesselI::BESSELI($x, $ord);
    }

    /**
     * BESSELJ.
     *
     *    Returns the Bessel function
     *
     *    Excel Function:
     *        BESSELJ(x,ord)
     *
     * @Deprecated 2.0.0 Use the BESSELJ() method in the Engineering\BesselJ class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELJ returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELJ returns the #VALUE! error value.
     *                                If $ord < 0, BESSELJ returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELJ($x, $ord)
    {
        return Engineering\BesselJ::BESSELJ($x, $ord);
    }

    /**
     * BESSELK.
     *
     *    Returns the modified Bessel function Kn(x), which is equivalent to the Bessel functions evaluated
     *        for purely imaginary arguments.
     *
     *    Excel Function:
     *        BESSELK(x,ord)
     *
     * @Deprecated 2.0.0 Use the BESSELK() method in the Engineering\BesselK class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELK returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELK returns the #VALUE! error value.
     *                                If $ord < 0, BESSELK returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELK($x, $ord)
    {
        return Engineering\BesselK::BESSELK($x, $ord);
    }

    /**
     * BESSELY.
     *
     * Returns the Bessel function, which is also called the Weber function or the Neumann function.
     *
     *    Excel Function:
     *        BESSELY(x,ord)
     *
     * @Deprecated 2.0.0 Use the BESSELY() method in the Engineering\BesselY class instead
     *
     * @param float $x The value at which to evaluate the function.
     *                                If x is nonnumeric, BESSELY returns the #VALUE! error value.
     * @param int $ord The order of the Bessel function. If n is not an integer, it is truncated.
     *                                If $ord is nonnumeric, BESSELY returns the #VALUE! error value.
     *                                If $ord < 0, BESSELY returns the #NUM! error value.
     *
     * @return float|string Result, or a string containing an error
     */
    public static function BESSELY($x, $ord)
    {
        return Engineering\BesselY::BESSELY($x, $ord);
    }

    /**
     * BINTODEC.
     *
     * Return a binary value as decimal.
     *
     * Excel Function:
     *        BIN2DEC(x)
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2DEC returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor((float) $x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            $x = substr($x, -9);

            return '-' . (512 - bindec($x));
        }

        return bindec($x);
    }

    /**
     * BINTOHEX.
     *
     * Return a binary value as hex.
     *
     * Excel Function:
     *        BIN2HEX(x[,places])
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2HEX returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2HEX uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2HEX returns the #VALUE! error value.
     *                                If places is negative, BIN2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        // Argument X
        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor((float) $x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            return str_repeat('F', 8) . substr(strtoupper(dechex((int) bindec(substr($x, -9)))), -2);
        }
        $hexVal = (string) strtoupper(dechex((int) bindec($x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * BINTOOCT.
     *
     * Return a binary value as octal.
     *
     * Excel Function:
     *        BIN2OCT(x[,places])
     *
     * @param string $x The binary number (as a string) that you want to convert. The number
     *                                cannot contain more than 10 characters (10 bits). The most significant
     *                                bit of number is the sign bit. The remaining 9 bits are magnitude bits.
     *                                Negative numbers are represented using two's-complement notation.
     *                                If number is not a valid binary number, or if number contains more than
     *                                10 characters (10 bits), BIN2OCT returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, BIN2OCT uses the
     *                                minimum number of characters necessary. Places is useful for padding the
     *                                return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, BIN2OCT returns the #VALUE! error value.
     *                                If places is negative, BIN2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function BINTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor((float) $x);
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[01]/', $x, $out)) {
            return Functions::NAN();
        }
        if (strlen($x) > 10) {
            return Functions::NAN();
        } elseif (strlen($x) == 10) {
            //    Two's Complement
            return str_repeat('7', 7) . substr(strtoupper(decoct((int) bindec(substr($x, -9)))), -3);
        }
        $octVal = (string) decoct((int) bindec($x));

        return self::nbrConversionFormat($octVal, $places);
    }

    /**
     * DECTOBIN.
     *
     * Return a decimal value as binary.
     *
     * Excel Function:
     *        DEC2BIN(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                valid place values are ignored and DEC2BIN returns a 10-character
     *                                (10-bit) binary number in which the most significant bit is the sign
     *                                bit. The remaining 9 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -512 or if number > 511, DEC2BIN returns the #NUM! error
     *                                value.
     *                                If number is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If DEC2BIN requires more than places characters, it returns the #NUM!
     *                                error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2BIN uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2BIN returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }

        $x = (int) floor((float) $x);
        if ($x < -512 || $x > 511) {
            return Functions::NAN();
        }

        $r = decbin($x);
        // Two's Complement
        $r = substr($r, -10);
        if (strlen($r) >= 11) {
            return Functions::NAN();
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * DECTOHEX.
     *
     * Return a decimal value as hex.
     *
     * Excel Function:
     *        DEC2HEX(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2HEX returns a 10-character (40-bit)
     *                                hexadecimal number in which the most significant bit is the sign
     *                                bit. The remaining 39 bits are magnitude bits. Negative numbers
     *                                are represented using two's-complement notation.
     *                                If number < -549,755,813,888 or if number > 549,755,813,887,
     *                                DEC2HEX returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If DEC2HEX requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2HEX uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2HEX returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (int) floor((float) $x);
        $r = strtoupper(dechex($x));
        if (strlen($r) == 8) {
            //    Two's Complement
            $r = 'FF' . $r;
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * DECTOOCT.
     *
     * Return an decimal value as octal.
     *
     * Excel Function:
     *        DEC2OCT(x[,places])
     *
     * @param string $x The decimal integer you want to convert. If number is negative,
     *                                places is ignored and DEC2OCT returns a 10-character (30-bit)
     *                                octal number in which the most significant bit is the sign bit.
     *                                The remaining 29 bits are magnitude bits. Negative numbers are
     *                                represented using two's-complement notation.
     *                                If number < -536,870,912 or if number > 536,870,911, DEC2OCT
     *                                returns the #NUM! error value.
     *                                If number is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If DEC2OCT requires more than places characters, it returns the
     *                                #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, DEC2OCT uses
     *                                the minimum number of characters necessary. Places is useful for
     *                                padding the return value with leading 0s (zeros).
     *                                If places is not an integer, it is truncated.
     *                                If places is nonnumeric, DEC2OCT returns the #VALUE! error value.
     *                                If places is zero or negative, DEC2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function DECTOOCT($x, $places = null)
    {
        $xorig = $x;
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            if (Functions::getCompatibilityMode() == Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[-0123456789.]/', $x, $out)) {
            return Functions::VALUE();
        }
        $x = (int) floor((float) $x);
        $r = decoct($x);
        if (strlen($r) == 11) {
            //    Two's Complement
            $r = substr($r, -10);
        }

        return self::nbrConversionFormat($r, $places);
    }

    /**
     * HEXTOBIN.
     *
     * Return a hex value as binary.
     *
     * Excel Function:
     *        HEX2BIN(x[,places])
     *
     * @param string $x the hexadecimal number you want to convert.
     *                  Number cannot contain more than 10 characters.
     *                  The most significant bit of number is the sign bit (40th bit from the right).
     *                  The remaining 9 bits are magnitude bits.
     *                  Negative numbers are represented using two's-complement notation.
     *                  If number is negative, HEX2BIN ignores places and returns a 10-character binary number.
     *                  If number is negative, it cannot be less than FFFFFFFE00,
     *                      and if number is positive, it cannot be greater than 1FF.
     *                  If number is not a valid hexadecimal number, HEX2BIN returns the #NUM! error value.
     *                  If HEX2BIN requires more than places characters, it returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    HEX2BIN uses the minimum number of characters necessary. Places
     *                                    is useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2BIN returns the #VALUE! error value.
     *                                    If places is negative, HEX2BIN returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::HEXTODEC($x), $places);
    }

    /**
     * HEXTODEC.
     *
     * Return a hex value as decimal.
     *
     * Excel Function:
     *        HEX2DEC(x)
     *
     * @param string $x The hexadecimal number you want to convert. This number cannot
     *                                contain more than 10 characters (40 bits). The most significant
     *                                bit of number is the sign bit. The remaining 39 bits are magnitude
     *                                bits. Negative numbers are represented using two's-complement
     *                                notation.
     *                                If number is not a valid hexadecimal number, HEX2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function HEXTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        if (strlen($x) > 10) {
            return Functions::NAN();
        }

        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(base_convert($char, 16, 2), 4, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 40 && $binX[0] == '1') {
            for ($i = 0; $i < 40; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    /**
     * HEXTOOCT.
     *
     * Return a hex value as octal.
     *
     * Excel Function:
     *        HEX2OCT(x[,places])
     *
     * @param string $x The hexadecimal number you want to convert. Number cannot
     *                                    contain more than 10 characters. The most significant bit of
     *                                    number is the sign bit. The remaining 39 bits are magnitude
     *                                    bits. Negative numbers are represented using two's-complement
     *                                    notation.
     *                                    If number is negative, HEX2OCT ignores places and returns a
     *                                    10-character octal number.
     *                                    If number is negative, it cannot be less than FFE0000000, and
     *                                    if number is positive, it cannot be greater than 1FFFFFFF.
     *                                    If number is not a valid hexadecimal number, HEX2OCT returns
     *                                    the #NUM! error value.
     *                                    If HEX2OCT requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, HEX2OCT
     *                                    uses the minimum number of characters necessary. Places is
     *                                    useful for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, HEX2OCT returns the #VALUE! error
     *                                    value.
     *                                    If places is negative, HEX2OCT returns the #NUM! error value.
     *
     * @return string
     */
    public static function HEXTOOCT($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (strlen($x) > preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out)) {
            return Functions::NAN();
        }

        $decimal = self::HEXTODEC($x);
        if ($decimal < -536870912 || $decimal > 536870911) {
            return Functions::NAN();
        }

        return self::DECTOOCT($decimal, $places);
    }

    /**
     * OCTTOBIN.
     *
     * Return an octal value as binary.
     *
     * Excel Function:
     *        OCT2BIN(x[,places])
     *
     * @param string $x The octal number you want to convert. Number may not
     *                                    contain more than 10 characters. The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits
     *                                    are magnitude bits. Negative numbers are represented
     *                                    using two's-complement notation.
     *                                    If number is negative, OCT2BIN ignores places and returns
     *                                    a 10-character binary number.
     *                                    If number is negative, it cannot be less than 7777777000,
     *                                    and if number is positive, it cannot be greater than 777.
     *                                    If number is not a valid octal number, OCT2BIN returns
     *                                    the #NUM! error value.
     *                                    If OCT2BIN requires more than places characters, it
     *                                    returns the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted,
     *                                    OCT2BIN uses the minimum number of characters necessary.
     *                                    Places is useful for padding the return value with
     *                                    leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2BIN returns the #VALUE!
     *                                    error value.
     *                                    If places is negative, OCT2BIN returns the #NUM! error
     *                                    value.
     *
     * @return string
     */
    public static function OCTTOBIN($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }

        return self::DECTOBIN(self::OCTTODEC($x), $places);
    }

    /**
     * OCTTODEC.
     *
     * Return an octal value as decimal.
     *
     * Excel Function:
     *        OCT2DEC(x)
     *
     * @param string $x The octal number you want to convert. Number may not contain
     *                                more than 10 octal characters (30 bits). The most significant
     *                                bit of number is the sign bit. The remaining 29 bits are
     *                                magnitude bits. Negative numbers are represented using
     *                                two's-complement notation.
     *                                If number is not a valid octal number, OCT2DEC returns the
     *                                #NUM! error value.
     *
     * @return string
     */
    public static function OCTTODEC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $binX = '';
        foreach (str_split($x) as $char) {
            $binX .= str_pad(decbin((int) $char), 3, '0', STR_PAD_LEFT);
        }
        if (strlen($binX) == 30 && $binX[0] == '1') {
            for ($i = 0; $i < 30; ++$i) {
                $binX[$i] = ($binX[$i] == '1' ? '0' : '1');
            }

            return (bindec($binX) + 1) * -1;
        }

        return bindec($binX);
    }

    /**
     * OCTTOHEX.
     *
     * Return an octal value as hex.
     *
     * Excel Function:
     *        OCT2HEX(x[,places])
     *
     * @param string $x The octal number you want to convert. Number may not contain
     *                                    more than 10 octal characters (30 bits). The most significant
     *                                    bit of number is the sign bit. The remaining 29 bits are
     *                                    magnitude bits. Negative numbers are represented using
     *                                    two's-complement notation.
     *                                    If number is negative, OCT2HEX ignores places and returns a
     *                                    10-character hexadecimal number.
     *                                    If number is not a valid octal number, OCT2HEX returns the
     *                                    #NUM! error value.
     *                                    If OCT2HEX requires more than places characters, it returns
     *                                    the #NUM! error value.
     * @param int $places The number of characters to use. If places is omitted, OCT2HEX
     *                                    uses the minimum number of characters necessary. Places is useful
     *                                    for padding the return value with leading 0s (zeros).
     *                                    If places is not an integer, it is truncated.
     *                                    If places is nonnumeric, OCT2HEX returns the #VALUE! error value.
     *                                    If places is negative, OCT2HEX returns the #NUM! error value.
     *
     * @return string
     */
    public static function OCTTOHEX($x, $places = null)
    {
        $x = Functions::flattenSingleValue($x);
        $places = Functions::flattenSingleValue($places);

        if (is_bool($x)) {
            return Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return Functions::NAN();
        }
        $hexVal = strtoupper(dechex((int) self::OCTTODEC((int) $x)));

        return self::nbrConversionFormat($hexVal, $places);
    }

    /**
     * COMPLEX.
     *
     * Converts real and imaginary coefficients into a complex number of the form x +/- yi or x +/- yj.
     *
     * Excel Function:
     *        COMPLEX(realNumber,imaginary[,suffix])
     *
     * @param float $realNumber the real coefficient of the complex number
     * @param float $imaginary the imaginary coefficient of the complex number
     * @param string $suffix The suffix for the imaginary component of the complex number.
     *                                        If omitted, the suffix is assumed to be "i".
     *
     * @return string
     */
    public static function COMPLEX($realNumber = 0.0, $imaginary = 0.0, $suffix = 'i')
    {
        $realNumber = ($realNumber === null) ? 0.0 : Functions::flattenSingleValue($realNumber);
        $imaginary = ($imaginary === null) ? 0.0 : Functions::flattenSingleValue($imaginary);
        $suffix = ($suffix === null) ? 'i' : Functions::flattenSingleValue($suffix);

        if (
            ((is_numeric($realNumber)) && (is_numeric($imaginary))) &&
            (($suffix == 'i') || ($suffix == 'j') || ($suffix == ''))
        ) {
            $complex = new Complex($realNumber, $imaginary, $suffix);

            return (string) $complex;
        }

        return Functions::VALUE();
    }

    /**
     * IMAGINARY.
     *
     * Returns the imaginary coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMAGINARY(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the imaginary
     *                                         coefficient
     *
     * @return float
     */
    public static function IMAGINARY($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getImaginary();
    }

    /**
     * IMREAL.
     *
     * Returns the real coefficient of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMREAL(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the real coefficient
     *
     * @return float
     */
    public static function IMREAL($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->getReal();
    }

    /**
     * IMABS.
     *
     * Returns the absolute value (modulus) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMABS(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the absolute value
     *
     * @return float
     */
    public static function IMABS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (new Complex($complexNumber))->abs();
    }

    /**
     * IMARGUMENT.
     *
     * Returns the argument theta of a complex number, i.e. the angle in radians from the real
     * axis to the representation of the number in polar coordinates.
     *
     * Excel Function:
     *        IMARGUMENT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the argument theta
     *
     * @return float|string
     */
    public static function IMARGUMENT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::DIV0();
        }

        return $complex->argument();
    }

    /**
     * IMCONJUGATE.
     *
     * Returns the complex conjugate of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCONJUGATE(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the conjugate
     *
     * @return string
     */
    public static function IMCONJUGATE($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->conjugate();
    }

    /**
     * IMCOS.
     *
     * Returns the cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOS(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cosine
     *
     * @return float|string
     */
    public static function IMCOS($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cos();
    }

    /**
     * IMCOSH.
     *
     * Returns the hyperbolic cosine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOSH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosine
     *
     * @return float|string
     */
    public static function IMCOSH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cosh();
    }

    /**
     * IMCOT.
     *
     * Returns the cotangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCOT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cotangent
     *
     * @return float|string
     */
    public static function IMCOT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->cot();
    }

    /**
     * IMCSC.
     *
     * Returns the cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSC(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the cosecant
     *
     * @return float|string
     */
    public static function IMCSC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csc();
    }

    /**
     * IMCSCH.
     *
     * Returns the hyperbolic cosecant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMCSCH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic cosecant
     *
     * @return float|string
     */
    public static function IMCSCH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->csch();
    }

    /**
     * IMSIN.
     *
     * Returns the sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSIN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the sine
     *
     * @return float|string
     */
    public static function IMSIN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sin();
    }

    /**
     * IMSINH.
     *
     * Returns the hyperbolic sine of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSINH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic sine
     *
     * @return float|string
     */
    public static function IMSINH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sinh();
    }

    /**
     * IMSEC.
     *
     * Returns the secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSEC(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the secant
     *
     * @return float|string
     */
    public static function IMSEC($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sec();
    }

    /**
     * IMSECH.
     *
     * Returns the hyperbolic secant of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSECH(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the hyperbolic secant
     *
     * @return float|string
     */
    public static function IMSECH($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->sech();
    }

    /**
     * IMTAN.
     *
     * Returns the tangent of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMTAN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the tangent
     *
     * @return float|string
     */
    public static function IMTAN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->tan();
    }

    /**
     * IMSQRT.
     *
     * Returns the square root of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSQRT(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the square root
     *
     * @return string
     */
    public static function IMSQRT($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $theta = self::IMARGUMENT($complexNumber);
        if ($theta === Functions::DIV0()) {
            return '0';
        }

        return (string) (new Complex($complexNumber))->sqrt();
    }

    /**
     * IMLN.
     *
     * Returns the natural logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLN(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the natural logarithm
     *
     * @return string
     */
    public static function IMLN($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->ln();
    }

    /**
     * IMLOG10.
     *
     * Returns the common logarithm (base 10) of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG10(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the common logarithm
     *
     * @return string
     */
    public static function IMLOG10($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log10();
    }

    /**
     * IMLOG2.
     *
     * Returns the base-2 logarithm of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMLOG2(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the base-2 logarithm
     *
     * @return string
     */
    public static function IMLOG2($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        $complex = new Complex($complexNumber);
        if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
            return Functions::NAN();
        }

        return (string) (new Complex($complexNumber))->log2();
    }

    /**
     * IMEXP.
     *
     * Returns the exponential of a complex number in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMEXP(complexNumber)
     *
     * @param string $complexNumber the complex number for which you want the exponential
     *
     * @return string
     */
    public static function IMEXP($complexNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);

        return (string) (new Complex($complexNumber))->exp();
    }

    /**
     * IMPOWER.
     *
     * Returns a complex number in x + yi or x + yj text format raised to a power.
     *
     * Excel Function:
     *        IMPOWER(complexNumber,realNumber)
     *
     * @param string $complexNumber the complex number you want to raise to a power
     * @param float $realNumber the power to which you want to raise the complex number
     *
     * @return string
     */
    public static function IMPOWER($complexNumber, $realNumber)
    {
        $complexNumber = Functions::flattenSingleValue($complexNumber);
        $realNumber = Functions::flattenSingleValue($realNumber);

        if (!is_numeric($realNumber)) {
            return Functions::VALUE();
        }

        return (string) (new Complex($complexNumber))->pow($realNumber);
    }

    /**
     * IMDIV.
     *
     * Returns the quotient of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMDIV(complexDividend,complexDivisor)
     *
     * @param string $complexDividend the complex numerator or dividend
     * @param string $complexDivisor the complex denominator or divisor
     *
     * @return string
     */
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        $complexDividend = Functions::flattenSingleValue($complexDividend);
        $complexDivisor = Functions::flattenSingleValue($complexDivisor);

        try {
            return (string) (new Complex($complexDividend))->divideby(new Complex($complexDivisor));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    /**
     * IMSUB.
     *
     * Returns the difference of two complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUB(complexNumber1,complexNumber2)
     *
     * @param string $complexNumber1 the complex number from which to subtract complexNumber2
     * @param string $complexNumber2 the complex number to subtract from complexNumber1
     *
     * @return string
     */
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        $complexNumber1 = Functions::flattenSingleValue($complexNumber1);
        $complexNumber2 = Functions::flattenSingleValue($complexNumber2);

        try {
            return (string) (new Complex($complexNumber1))->subtract(new Complex($complexNumber2));
        } catch (ComplexException $e) {
            return Functions::NAN();
        }
    }

    /**
     * IMSUM.
     *
     * Returns the sum of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMSUM(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to add
     *
     * @return string
     */
    public static function IMSUM(...$complexNumbers)
    {
        // Return value
        $returnValue = new Complex(0.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->add(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    /**
     * IMPRODUCT.
     *
     * Returns the product of two or more complex numbers in x + yi or x + yj text format.
     *
     * Excel Function:
     *        IMPRODUCT(complexNumber[,complexNumber[,...]])
     *
     * @param string ...$complexNumbers Series of complex numbers to multiply
     *
     * @return string
     */
    public static function IMPRODUCT(...$complexNumbers)
    {
        // Return value
        $returnValue = new Complex(1.0);
        $aArgs = Functions::flattenArray($complexNumbers);

        try {
            // Loop through the arguments
            foreach ($aArgs as $complex) {
                $returnValue = $returnValue->multiply(new Complex($complex));
            }
        } catch (ComplexException $e) {
            return Functions::NAN();
        }

        return (string) $returnValue;
    }

    /**
     * DELTA.
     *
     * Tests whether two values are equal. Returns 1 if number1 = number2; returns 0 otherwise.
     *    Use this function to filter a set of values. For example, by summing several DELTA
     *    functions you calculate the count of equal pairs. This function is also known as the
     * Kronecker Delta function.
     *
     *    Excel Function:
     *        DELTA(a[,b])
     *
     * @param float $a the first number
     * @param float $b The second number. If omitted, b is assumed to be zero.
     *
     * @return int
     */
    public static function DELTA($a, $b = 0)
    {
        $a = Functions::flattenSingleValue($a);
        $b = Functions::flattenSingleValue($b);

        return (int) ($a == $b);
    }

    /**
     * GESTEP.
     *
     *    Excel Function:
     *        GESTEP(number[,step])
     *
     *    Returns 1 if number >= step; returns 0 (zero) otherwise
     *    Use this function to filter a set of values. For example, by summing several GESTEP
     * functions you calculate the count of values that exceed a threshold.
     *
     * @param float $number the value to test against step
     * @param float $step The threshold value.
     *                                    If you omit a value for step, GESTEP uses zero.
     *
     * @return int
     */
    public static function GESTEP($number, $step = 0)
    {
        $number = Functions::flattenSingleValue($number);
        $step = Functions::flattenSingleValue($step);

        return (int) ($number >= $step);
    }

    //
    //    Private method to calculate the erf value
    //
    private static $twoSqrtPi = 1.128379167095512574;

    public static function erfVal($x)
    {
        if (abs($x) > 2.2) {
            return 1 - self::erfcVal($x);
        }
        $sum = $term = $x;
        $xsqr = ($x * $x);
        $j = 1;
        do {
            $term *= $xsqr / $j;
            $sum -= $term / (2 * $j + 1);
            ++$j;
            $term *= $xsqr / $j;
            $sum += $term / (2 * $j + 1);
            ++$j;
            if ($sum == 0.0) {
                break;
            }
        } while (abs($term / $sum) > Functions::PRECISION);

        return self::$twoSqrtPi * $sum;
    }

    /**
     * BITAND.
     *
     * Returns the bitwise AND of two integer values.
     *
     * Excel Function:
     *        BITAND(number1, number2)
     *
     * @Deprecated 2.0.0 Use the BITAND() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITAND($number1, $number2)
    {
        return BitWise::BITAND($number1, $number2);
    }

    /**
     * BITOR.
     *
     * Returns the bitwise OR of two integer values.
     *
     * Excel Function:
     *        BITOR(number1, number2)
     *
     * @Deprecated 2.0.0 Use the BITOR() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITOR($number1, $number2)
    {
        return BitWise::BITOR($number1, $number2);
    }

    /**
     * BITXOR.
     *
     * Returns the bitwise XOR of two integer values.
     *
     * Excel Function:
     *        BITXOR(number1, number2)
     *
     * @Deprecated 2.0.0 Use the BITXOR() method in the Engineering\BitWise class instead
     *
     * @param int $number1
     * @param int $number2
     *
     * @return int|string
     */
    public static function BITXOR($number1, $number2)
    {
        return BitWise::BITXOR($number1, $number2);
    }

    /**
     * BITLSHIFT.
     *
     * Returns the number value shifted left by shift_amount bits.
     *
     * Excel Function:
     *        BITLSHIFT(number, shift_amount)
     *
     * @Deprecated 2.0.0 Use the BITLSHIFT() method in the Engineering\BitWise class instead
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITLSHIFT($number, $shiftAmount)
    {
        return BitWise::BITLSHIFT($number, $shiftAmount);
    }

    /**
     * BITRSHIFT.
     *
     * Returns the number value shifted right by shift_amount bits.
     *
     * Excel Function:
     *        BITRSHIFT(number, shift_amount)
     *
     * @Deprecated 2.0.0 Use the BITRSHIFT() method in the Engineering\BitWise class instead
     *
     * @param int $number
     * @param int $shiftAmount
     *
     * @return int|string
     */
    public static function BITRSHIFT($number, $shiftAmount)
    {
        return BitWise::BITRSHIFT($number, $shiftAmount);
    }

    /**
     * ERF.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the upper or lower bound arguments,
     *            the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *            improved, so that it can now calculate the function for both positive and negative ranges.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts negative arguments.
     *
     *    Excel Function:
     *        ERF(lower[,upper])
     *
     * @param float $lower lower bound for integrating ERF
     * @param float $upper upper bound for integrating ERF.
     *                                If omitted, ERF integrates between zero and lower_limit
     *
     * @return float|string
     */
    public static function ERF($lower, $upper = null)
    {
        $lower = Functions::flattenSingleValue($lower);
        $upper = Functions::flattenSingleValue($upper);

        if (is_numeric($lower)) {
            if ($upper === null) {
                return self::erfVal($lower);
            }
            if (is_numeric($upper)) {
                return self::erfVal($upper) - self::erfVal($lower);
            }
        }

        return Functions::VALUE();
    }

    /**
     * ERFPRECISE.
     *
     * Returns the error function integrated between the lower and upper bound arguments.
     *
     *    Excel Function:
     *        ERF.PRECISE(limit)
     *
     * @param float $limit bound for integrating ERF
     *
     * @return float|string
     */
    public static function ERFPRECISE($limit)
    {
        $limit = Functions::flattenSingleValue($limit);

        return self::ERF($limit);
    }

    //
    //    Private method to calculate the erfc value
    //
    private static $oneSqrtPi = 0.564189583547756287;

    private static function erfcVal($x)
    {
        if (abs($x) < 2.2) {
            return 1 - self::erfVal($x);
        }
        if ($x < 0) {
            return 2 - self::ERFC(-$x);
        }
        $a = $n = 1;
        $b = $c = $x;
        $d = ($x * $x) + 0.5;
        $q1 = $q2 = $b / $d;
        $t = 0;
        do {
            $t = $a * $n + $b * $x;
            $a = $b;
            $b = $t;
            $t = $c * $n + $d * $x;
            $c = $d;
            $d = $t;
            $n += 0.5;
            $q1 = $q2;
            $q2 = $b / $d;
        } while ((abs($q1 - $q2) / $q2) > Functions::PRECISION);

        return self::$oneSqrtPi * exp(-$x * $x) * $q2;
    }

    /**
     * ERFC.
     *
     *    Returns the complementary ERF function integrated between x and infinity
     *
     *    Note: In Excel 2007 or earlier, if you input a negative value for the lower bound argument,
     *        the function would return a #NUM! error. However, in Excel 2010, the function algorithm was
     *        improved, so that it can now calculate the function for both positive and negative x values.
     *            PhpSpreadsheet follows Excel 2010 behaviour, and accepts nagative arguments.
     *
     *    Excel Function:
     *        ERFC(x)
     *
     * @param float $x The lower bound for integrating ERFC
     *
     * @return float|string
     */
    public static function ERFC($x)
    {
        $x = Functions::flattenSingleValue($x);

        if (is_numeric($x)) {
            return self::erfcVal($x);
        }

        return Functions::VALUE();
    }

    /**
     *    getConversionGroups
     * Returns a list of the different conversion groups for UOM conversions.
     *
     * @Deprecated 2.0.0 Use the getConversionCategories() method in the Engineering\ConvertUOM class instead
     *
     * @return array
     */
    public static function getConversionGroups()
    {
        return Engineering\ConvertUOM::getConversionCategories();
    }

    /**
     *    getConversionGroupUnits
     * Returns an array of units of measure, for a specified conversion group, or for all groups.
     *
     * @Deprecated Use the getConversionCategoryUnits() method in the ConvertUOM class instead
     *
     * @param null|mixed $category
     *
     * @return array
     */
    public static function getConversionGroupUnits($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnits($category);
    }

    /**
     * getConversionGroupUnitDetails.
     *
     * @Deprecated Use the getConversionCategoryUnitDetails() method in the ConvertUOM class instead
     *
     * @param null|mixed $category
     *
     * @return array
     */
    public static function getConversionGroupUnitDetails($category = null)
    {
        return Engineering\ConvertUOM::getConversionCategoryUnitDetails($category);
    }

    /**
     *    getConversionMultipliers
     * Returns an array of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM().
     *
     * @Deprecated Use the getConversionMultipliers() method in the ConvertUOM class instead
     *
     * @return array of mixed
     */
    public static function getConversionMultipliers()
    {
        return Engineering\ConvertUOM::getConversionMultipliers();
    }

    /**
     *    getBinaryConversionMultipliers
     * Returns an array of the additional Multiplier prefixes that can be used with Information Units of Measure in CONVERTUOM().
     *
     * @Deprecated Use the getBinaryConversionMultipliers() method in the ConvertUOM class instead
     *
     * @return array of mixed
     */
    public static function getBinaryConversionMultipliers()
    {
        return Engineering\ConvertUOM::getBinaryConversionMultipliers();
    }

    /**
     * CONVERTUOM.
     *
     * Converts a number from one measurement system to another.
     *    For example, CONVERT can translate a table of distances in miles to a table of distances
     * in kilometers.
     *
     *    Excel Function:
     *        CONVERT(value,fromUOM,toUOM)
     *
     * @Deprecated Use the CONVERT() method in the ConvertUOM class instead
     *
     * @param float|int $value the value in fromUOM to convert
     * @param string $fromUOM the units for value
     * @param string $toUOM the units for the result
     *
     * @return float|string
     */
    public static function CONVERTUOM($value, $fromUOM, $toUOM)
    {
        return Engineering\ConvertUOM::CONVERT($value, $fromUOM, $toUOM);
    }
}
