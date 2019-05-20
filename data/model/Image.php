<?php
namespace Capstone;


class Image
{
    private $image_id;
    private $img_uri;
    private $height;
    private $width;
    private $format;

    public function __construct($id, $uri)
    {
        $this->image_id = $id;
        $this->img_uri = $uri;
    }

    public static function buildEmpty()
    {
        return
            new Image(null, null);
    }

    /**
     * @return mixed
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * @return mixed
     */
    public function getImgUri()
    {
        return $this->img_uri;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return mixed
     */
    public function getFormat()
    {
        return $this->format;
    }
}