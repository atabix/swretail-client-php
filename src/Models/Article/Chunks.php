<?php

namespace SWRetail\Models\Article;

use OutOfBoundsException;
use SWRetail\Http\Client;
use SWRetail\Http\Response;
use SWRetail\Models\Article;
use SWRetail\Models\Model;

class Chunks extends Model
{
    protected $chunk;
    protected $nextPage = 0;

    public function __construct()
    {
        //
    }

    /**
     * Retrieve articles for the current/provided chunk.
     *
     * @param int $chunk Chunk pointer, current when empty.
     *
     * @return array[Article]
     */
    public function get(int $chunk = null)
    {
        $chunk = $chunk ?? $this->chunk;

        $path = 'article/-1/' . $chunk;

        $response = Client::requestApi('GET', $path);

        return $this->handleChunkResponse($response);
    }

    /**
     * Reset the chunk pointer.
     *
     * @return int
     */
    public function first() : int
    {
        $this->chunk = 0;
        $this->nextPage = 0;

        return $this->chunk;
    }

    /**
     * Increase the chunk pointer.
     *
     * @throws OutOfBoundsException
     *
     * @return int
     */
    public function next() : int
    {
        if ($this->nextPage == -1) {
            throw new OutOfBoundsException('No more chunks');
        }

        $this->chunk = \is_null($this->chunk) ? 0 : $this->nextPage;

        return $this->chunk;
    }

    /**
     * Increase the chunk pointer and retrieve articles.
     *
     * @throws OutOfBoundsException
     *
     * @return array[Article]
     */
    public function getNext()
    {
        $chunk = $this->next();

        return $this->get($chunk);
    }

    /**
     * Handle search responses for a multiple results.
     *
     * @param Response $response
     *
     * @return array[Article]
     */
    public function handleChunkResponse(Response $response) : array
    {
        // errorcode == 0 means "empty result" (not an exception).
        if (isset($response->json->errorcode)) {
            return [];
        }

        $this->nextPage = (int) $response->json->next_page;

        $list = [];
        foreach ($response->json as $key => $data) {
            if ($key == 'next_page' || ! isset($data->article_number)) {
                continue;
            }
            $article = new Article($data->article_number, $data->article_season);
            $article->parseData($data);
            $list[$article->getId()] = $article;
        }

        return $list;
    }

    /**
     * Get all articles through a generator.
     *
     * Do not store the data internally;
     * Make sequential API request when needed for next chunk;
     *
     * @return \Generator
     */
    public function yieldAll()
    {
        $this->first();

        while ($this->nextPage >= 0) {
            $list = $this->get($this->chunk);

            foreach ($list as $id => $article) {
                yield $id => $article;
            }

            try {
                $this->next();
            } catch (OutOfBoundsException $e) {
                break;
            }
        }
    }
}
