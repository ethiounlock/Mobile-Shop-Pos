<?php

namespace Sabberworm\CSS\Comment;

interface Commentable
{
    /**
     * Adds an array of comments to the object.
     *
     * @param Comment[] $aComments An array of Comment objects.
     */
    public function addComments(array $aComments): void;

    /**
     * Returns an array of comments associated with the object.
     *
     * @return Comment[] An array of Comment objects.
     */
    public function getComments(): array;

    /**
     * Sets the array of comments associated with the object.
     *
     * @param Comment[] $aComments An array of Comment objects.
     */
    public function setComments(array $aComments): void;
}
