<?php



namespace iutnc\netvod\action;


use iutnc\netvod\db\ConnectionFactory;
use iutnc\netvod\preference\Preferences;
use iutnc\netvod\render\Renderer;
use iutnc\netvod\render\SerieRenderer;
use iutnc\netvod\tri\TriCriteres;
use iutnc\netvod\tri\TriMotCle;
use iutnc\netvod\video\Episode;
use iutnc\netvod\video\Serie;
use PDOException;
use PDOStatement;


class DisplayCatalogueAction extends Action
{
    /**
     * @return string
     */
    public function execute(): string
    {
        $html = '';
        if (isset($_SESSION['user_connected'])) {
            $user = unserialize($_SESSION['user_connected']);
            if ($user->active === 1) {
                $html = '<div class="catalogue-action-group">
                         <div class="catalogue-action-trie">';
                $html .= DisplayTriAction::execute($this->http_method);
                $html .= '</div>
                          <div class="catalogue-action-title">';
                $html .= "<h1> Notre catalogue : </h1>";
                $html .= '</div>
                            <div class="catalogue-action-item">';
                $idSeries = Serie::getAllIdSerie();
                foreach ($idSeries as $id) {
                    $titre = Serie::getTitre($id);
                    $html .= $this->getHtml($titre, $id);
                }
                $html .= '</div>';
                $html .= '</div>';
            } else {
                $html = "Le compte doit être activé pour utiliser cette fonctionnalité";
            }
        }
        else
        {
            $html = "Il faut se connecter avant d'accéder au site";
        }
        return $html;
    }


    /**
     * @param array $idSeries
     * @return string
     */
    public function afficherCatalogue(array $idSeries=[]):string
    {


    }

    /**
     * @param string $titre
     * @param int $id
     * @return string
     */
    private function getHtml(string $titre,int $id): string
    {
        $listeEpisode = Episode::getEpisodeByIdSeries($id);
        $serie = new Serie($titre, $listeEpisode);
        $renderer = new SerieRenderer($serie);

        $favoris = "";
        return "<a href='?action=display-serie&id={$id}$favoris'>{$renderer->render(Renderer::COMPACTWITHIMGFORCATALOGUE)} </a><br>";
    }
}