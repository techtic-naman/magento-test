<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */

namespace Webkul\Helpdesk\Model\Mail;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     * @var string
     */
    protected $_charset = 'iso-8859-1';

    /**
     * @var string
     */
  //  protected $_headerEncoding = \Zend_Mime::ENCODING_QUOTEDPRINTABLE;

    /**
     * @var array
     */
    protected $_headers = [];

    /**
     * Sets the HTML body for the message
     *
     * @param  string $html
     * @param  string $charset
     * @param  string $encoding
     * @return Zend_Mail Provides fluent interface
     */
    public function setBodyHtml($html, $charset = null, $encoding = \Laminas\Mime\Mime::ENCODING_QUOTEDPRINTABLE)
    {
        if ($charset === null) {
            $charset = $this->_charset;
        }

        $mp = new \Laminas\Mime\Part($html);
        $mp->encoding = $encoding;
        $mp->type = \Laminas\Mime\Mime::TYPE_HTML;
        $mp->disposition = \Laminas\Mime\Mime::DISPOSITION_INLINE;
        $mp->charset = $charset;
        $this->_bodyHtml = $mp;
        return $this;
    }

    /**
     * Add attachment
     *
     * @param mixed  $content
     * @param object $attachment
     */
    public function addAttachment($content, $attachment)
    {
        $this->message->createAttachment(
            $content,
            $attachment->type,
            $attachment->disposition,
            $attachment->encoding,
            $attachment->filename
        );
        return $this;
    }

    /**
     * Add a custom header to the message
     *
     * @param  string  $name
     * @param  string  $value
     * @param  boolean $append
    * @return \Laminas\Mail\Message  Provides fluent interface
    * @throws \Laminas\Mail\Exception\InvalidArgumentException on attempts to create standard headers
     */
    public function addHeader($name, $value, $append = false)
    {
        $prohibit = [
            'to', 'cc', 'bcc', 'from', 'subject',
            'reply-to', 'return-path',
            'date', 'message-id',
        ];
        if (in_array(strtolower($name), $prohibit)) {
            /**
             * @see Zend_Mail_Exception
             */
            throw new \Laminas\Mail\Exception\InvalidArgumentException('Cannot set standard header from addHeader()');
        }

        $value = $this->_filterOther($value);
        $value = $this->_encodeHeader($value);
        $this->_storeHeader($name, $value, $append);

        return $this;
    }

    /**
     * Mime_content_type Return Mime type
     *
     * @param  String $filename File Name
     * @return String Mime type
     */
    public function getMimeContentType($filename)
    {
        $mimeTypesArr = [
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',

            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime'
        ];
        $tmp = explode('.', $filename);
        $ext = strtolower(array_pop($tmp));
        if (array_key_exists($ext, $mimeTypesArr)) {
            return $mimeTypesArr[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * Filter of other data
     *
     * @param  string $data
     * @return string
     */
    protected function _filterOther($data)
    {
        $rule = [
            "\r" => '',
            "\n" => '',
            "\t" => '',
        ];
        return strtr($data, $rule);
    }

    /**
     * Encode header fields
     *
     * Encodes header content according to RFC1522 if it contains non-printable
     * characters.
     *
     * @param  string $value
     * @return string
     */
    protected function _encodeHeader($value)
    {
        if (\Laminas\Mime\Mime::isPrintable($value) === false) {
            if ($this->getHeaderEncoding() === \Laminas\Mime\Mime::ENCODING_QUOTEDPRINTABLE) {
            $value = \Laminas\Mime\Mime::encodeQuotedPrintableHeader(
                $value,
                $this->getCharset(),
                \Laminas\Mime\Mime::LINELENGTH,
                \Laminas\Mime\Mime::LINEEND
            );
            } else {
            $value = \Laminas\Mime\Mime::encodeBase64Header(
                $value,
                $this->getCharset(),
                \Laminas\Mime\Mime::LINELENGTH,
                \Laminas\Mime\Mime::LINEEND
            );
            }
        }

        return $value;
    }

    /**
     * Return the encoding of mail headers
     *
     * Either Zend_Mime::ENCODING_QUOTEDPRINTABLE or Zend_Mime::ENCODING_BASE64
     *
     * @return string
     */
    public function getHeaderEncoding()
    {
        return $this->_headerEncoding;
    }

    /**
     * Return charset string
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * Add a header to the message
     *
     * Adds a header to this message. If append is true and the header already
     * exists, raises a flag indicating that the header should be appended.
     *
     * @param string $headerName
     * @param string $value
     * @param bool   $append
     */
    protected function _storeHeader($headerName, $value, $append = false)
    {
        if (isset($this->_headers[$headerName])) {
            $this->_headers[$headerName][] = $value;
        } else {
            $this->_headers[$headerName] = [$value];
        }

        if ($append) {
            $this->_headers[$headerName]['append'] = true;
        }
    }
}
