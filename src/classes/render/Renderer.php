<?php



namespace iutnc\netvod\render;



interface Renderer
{
    const COMPACT = 1;
    const LONG = 2;
    const COMPACTWITHIMG = 3;
    const COMPACTWITHIMGFORCATALOGUE = 4;

    public function render(int $selector) : String;
}