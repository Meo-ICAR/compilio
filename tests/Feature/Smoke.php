<?php

it('has smoke page', function () {
    $response = $this->get('/smoke');

    $response->assertStatus(200);
});
