<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_artist extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        // Artists
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "represented"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "null"=>FALSE,
                "default"=>1
            ),
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE,
                "null"=>true
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("artist");
                
        // Exhibition artists link
        $this->dbforge->add_field(array(
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->add_key("artist_id",TRUE);
        $this->dbforge->create_table("artist_exhibition");
        $this->db->query("ALTER TABLE artist_exhibition ADD FOREIGN KEY exhibition_id_fk (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE artist_exhibition ADD FOREIGN KEY artist_id_fk (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->db->insert_batch("artist",array(
            array(
                "id"=>"sylvie-belanger",
				"name"=>"Sylvie Bélanger",
				"represented"=>1
            ),
            array(
                "id"=>"cathy-daley",
				"name"=>"Cathy Daley",
				"represented"=>1
            ),
            array(
                "id"=>"paul-de-guzman",
				"name"=>"Paul de Guzman",
				"represented"=>1
            ),
            array(
                "id"=>"michelle-gay",
				"name"=>"Michelle Gay",
				"represented"=>1
            ),
            array(
                "id"=>"eric-glavin",
				"name"=>"Eric Glavin",
				"represented"=>1
            ),
            array(
                "id"=>"charles-goldman",
				"name"=>"Charles Goldman",
				"represented"=>1
            ),
            array(
                "id"=>"martin-golland",
				"name"=>"Martin Golland",
				"represented"=>1
            ),
            array(
                "id"=>"lee-goreas",
				"name"=>"Lee Goreas",
				"represented"=>1
            ),
            array(
                "id"=>"will-gorlitz",
				"name"=>"Will Gorlitz",
				"represented"=>1
            ),
            array(
                "id"=>"toni-hafkenscheid",
				"name"=>"Toni Hafkenscheid",
				"represented"=>1
            ),
            array(
                "id"=>"luis-jacob",
				"name"=>"Luis Jacob",
				"represented"=>1
            ),
            array(
                "id"=>"ginette-legare",
				"name"=>"Ginette Legaré",
				"represented"=>1
            ),
            array(
                "id"=>"micah-lexier",
				"name"=>"Micah Lexier",
				"represented"=>1
            ),
            array(
                "id"=>"euan-macdonald",
				"name"=>"Euan Macdonald",
				"represented"=>1
            ),
            array(
                "id"=>"james-nizam",
				"name"=>"James Nizam",
				"represented"=>1
            ),
            array(
                "id"=>"louise-noguchi",
				"name"=>"Louise Noguchi",
				"represented"=>1
            ),
            array(
                "id"=>"andy-patton",
				"name"=>"Andy Patton",
				"represented"=>1
            ),
            array(
                "id"=>"ed-pien",
				"name"=>"Ed Pien",
				"represented"=>1
            ),
            array(
                "id"=>"jaan-poldaas",
				"name"=>"Jaan Poldaas",
				"represented"=>1
            ),
            array(
                "id"=>"nicholas-sheila-pye",
				"name"=>"Nicholas & Sheila Pye",
				"represented"=>1
            ),
            array(
                "id"=>"steve-reinke",
				"name"=>"Steve Reinke",
				"represented"=>1
            ),
            array(
                "id"=>"kelly-richardson",
				"name"=>"Kelly Richardson",
				"represented"=>1
            ),
            array(
                "id"=>"mitch-robertson",
				"name"=>"Mitch Robertson",
				"represented"=>1
            ),
            array(
                "id"=>"gina-rorai",
				"name"=>"Gina Rorai",
				"represented"=>1
            ),
            array(
                "id"=>"howard-simkins",
				"name"=>"Howard Simkins",
				"represented"=>1
            ),
            array(
                "id"=>"richard-storms",
				"name"=>"Richard Storms",
				"represented"=>1
            ),
            array(
                "id"=>"shaan-syed",
				"name"=>"Shaan Syed",
				"represented"=>1
            ),
            array(
                "id"=>"renee-van-halm",
				"name"=>"Renee Van Halm",
				"represented"=>1
            ),
            array(
                "id"=>"ben-walmsley",
				"name"=>"Ben Walmsley",
				"represented"=>1
            ),
            array(
                "id"=>"janet-werner",
				"name"=>"Janet Werner",
				"represented"=>1
            ),
            array(
                "id"=>"mathieu-gaudet",
				"name"=>"Mathieu Gaudet",
				"represented"=>0
            ),
            array(
                "id"=>"ingo-gerken",
				"name"=>"Ingo Gerken",
				"represented"=>0
            ),
            array(
                "id"=>"john-massier",
				"name"=>"John Massier",
				"represented"=>0
            ),
            array(
                "id"=>"luce-meunier",
				"name"=>"Luce Meunier",
				"represented"=>0
            ),
            array(
                "id"=>"nadia-myre",
				"name"=>"Nadia Myre",
				"represented"=>0
            ),
            array(
                "id"=>"juan-oritz-apuy",
				"name"=>"Juan Oritz-Apuy",
				"represented"=>0
            ),
            array(
                "id"=>"peter-smith",
				"name"=>"Peter Smith",
				"represented"=>0
            ),
            array(
                "id"=>"martha-townsend",
				"name"=>"Martha Townsend",
				"represented"=>0
            ),
            array(
                "id"=>"michael-voss",
				"name"=>"Michael Voss",
				"represented"=>0
            )
        ));
    }
    
    public function down() {
        $this->dbforge->drop_table("artist_exhibition");
        $this->dbforge->drop_table("artist");
    }
}