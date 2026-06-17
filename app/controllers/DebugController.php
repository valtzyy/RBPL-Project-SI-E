<?php
// app/controllers/DebugController.php

class DebugController extends Controller
{
    public function index()
    {
        $this->view('debug_panel');
    }
}
