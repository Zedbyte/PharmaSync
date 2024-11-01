<?php

use App\Models\BaseModel;

function returnDBCon(BaseModel $object) {
    return $object->getDBCon();
}