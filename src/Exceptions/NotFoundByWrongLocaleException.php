<?php

namespace Nevadskiy\LocalizationRouter\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotFoundByWrongLocaleException extends Exception
{
    /**
     * @var string
     */
    private $correctUri;

    /**
     * NotFoundByWrongLocaleException constructor.
     *
     * @param NotFoundHttpException $e
     * @param string $correctUri
     */
    public function __construct(NotFoundHttpException $e, string $correctUri)
    {
        parent::__construct($e->getMessage(), $e->getCode(), $e->getPrevious());
        $this->correctUri = $correctUri;
    }

    /**
     * Create the exception with url.
     *
     * @param NotFoundHttpException $e
     * @param string $correctUri
     * @return NotFoundByWrongLocaleException
     */
    public static function withUri(NotFoundHttpException $e, string $correctUri): NotFoundByWrongLocaleException
    {
        return new static($e, $correctUri);
    }

    /**
     * Redirect to the correct url.
     *
     * @return RedirectResponse|mixed
     */
    public function redirect()
    {
        return redirect()->to($this->correctUri, Response::HTTP_FOUND, ['Vary' => 'Accept-Language']);
    }
}
