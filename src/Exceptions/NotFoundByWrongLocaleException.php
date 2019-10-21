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
    private $correctUrl;

    /**
     * NotFoundByWrongLocaleException constructor.
     *
     * @param NotFoundHttpException $e
     * @param string $correctUrl
     */
    public function __construct(NotFoundHttpException $e, string $correctUrl)
    {
        parent::__construct($e->getMessage(), $e->getCode(), $e->getPrevious());
        $this->correctUrl = $correctUrl;
    }

    /**
     * Create the exception with url.
     *
     * @param NotFoundHttpException $e
     * @param string $correctUrl
     * @return NotFoundByWrongLocaleException
     */
    public static function withUrl(NotFoundHttpException $e, string $correctUrl): NotFoundByWrongLocaleException
    {
        return new static($e, $correctUrl);
    }

    /**
     * Redirect to the correct url.
     *
     * @return RedirectResponse|mixed
     */
    public function redirect()
    {
        return redirect()->to($this->correctUrl, Response::HTTP_FOUND, ['Vary' => 'Accept-Language']);
    }
}
