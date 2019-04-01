<?php
/**
 * @desc ECB模式Des加密
 * @author huzw
 * @param
 *      <p>@time 2017.11.15</p>
 *      <p>@remark
 *          - 和公司所用C++ Des加密保持一致性
 *      </p>
 **/
class ECB_Des
{
    const VERSION = 'v1.0.17.1115';
    
    /**
     * @desc 静态方式加密
     * @param
     *      <p>$input * 待加密的字符串</p>
     *      <p>$authKey 密钥，最多传入8个有效字符，缺省空字符串</p>
     **/
    public static function encrypt($input, $authKey = '')
    {
        try {
            $mod = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
            $mod_len = mcrypt_enc_get_key_size($mod);
            $authKey = substr ($authKey, 0, $mod_len);
            
            /* srand();
            $iv = mcrypt_create_iv($mod_len, MCRYPT_RAND); */
            
            $iv = '00000000';
            mcrypt_generic_init($mod, $authKey, $iv);
            $output = mcrypt_generic($mod, $input);
            mcrypt_generic_deinit($mod);
            mcrypt_module_close($mod);
            
            return self::removeBR(base64_encode($output)); 
        } 
        catch (Exception $e) {
            return '';
        }
    }

    /**
     * @desc 静态方式解密
     * @param
     *      <p>$input * 待解密的字符串</p>
     *      <p>$authKey 密钥，最多传入8个有效字符，缺省空字符串</p>
     **/
    public static function decrypt($input, $authKey = '')
    {
        try {
            $input = base64_decode($input);
            
            $mod = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
            $mod_len = mcrypt_enc_get_key_size($mod);
            $authKey = substr ($authKey, 0, $mod_len);
            
            /* $iv_size = mcrypt_enc_get_iv_size($mod);
            $iv = substr($input, 0, $iv_size); */
            
            $iv = '00000000';
            mcrypt_generic_init($mod, $authKey, $iv);
            $output = mdecrypt_generic($mod, $input);
            mcrypt_generic_deinit($mod);
            mcrypt_module_close($mod);
            
            return rtrim($output, '\0');
        } catch (Exception $e) {
            return '';
        }
    }
    /**
     * @desc 删除回车和换行
     * @param
     *      <p>$_input * 待处理的字符串</p>
     **/
    public static function removeBR($_input = '')
    {
        try {
            $_output = '';
            
            $_input_len = strlen($_input);
            
            $_input_str = str_split($_input);
            
            for ($i = 0; $i < $_input_len; $i++ )
            {
                if ($_input_str[$i] != '\n' and $_input_str[$i] != '\r')
                {
                    $_output .= $_input_str[$i];
                }
            }
            
            return $_output;
            
        } catch (Exception $e) {
            return $_input;
        }
    }
}
?>