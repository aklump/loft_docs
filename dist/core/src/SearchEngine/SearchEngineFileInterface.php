<?php

namespace AKlump\LoftDocs\SearchEngine;

interface SearchEngineFileInterface {

  public function getFilename(): string;

  public function getContents(): string;
}
