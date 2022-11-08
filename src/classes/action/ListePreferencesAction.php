<?php

namespace iutnc\netvod\action;

class ListePreferencesAction extends Action
{

    public function execute(): string
    {
        $html = "";
        if (isset($_SESSION["userConnected"])) {
            $user = $_SESSION["userConnected"];
            $preferences = $user->preferences;
            $seriesPref = $preferences->series;
            $html = <<<END
                <div>
                    <a href="index.php?action=">
                        <table id="champ">
                            <tr>
                                <td><p>Hello</p></td>
                                <td><p>Hello2</p></td>
                            </tr>
                        </table>
                    </a>
                </div>
            END;

            foreach ($seriesPref as $value) {
                $renderer = ""; //new SerieRenderer($value);
                $html .= $renderer->render();
            }
        }else {

        }
    }
}