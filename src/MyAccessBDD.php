<?php
include_once("AccessBDD.php");

class MyAccessBDD extends AccessBDD {

    public function __construct(){
        try{
            parent::__construct();
        }catch(\Exception $e){
            throw $e;
        }
    }

    protected function traitementSelect(string $table, ?array $champs) : ?array{
        switch($table){
            case "livre" :
                return $this->selectAllLivres();
            case "dvd" :
                return $this->selectAllDvd();
            case "revue" :
                return $this->selectAllRevues();
            case "exemplaire" :
                return $this->selectExemplairesRevue($champs);
            case "commandeslivre" :
                return $this->selectCommandesLivreDvd($champs);
            case "commandesdvd" :
                return $this->selectCommandesLivreDvd($champs);
            case "suivi" :
                return $this->selectTableSimple($table);
            case "genre" :
            case "public" :
            case "rayon" :
            case "etat" :
                return $this->selectTableSimple($table);
            case "" :
            default:
                return $this->selectTuplesOneTable($table, $champs);
        }
    }

    protected function traitementInsert(string $table, ?array $champs) : ?int{
        switch($table){
            case "" :
            default:
                return $this->insertOneTupleOneTable($table, $champs);
        }
    }

    protected function traitementUpdate(string $table, ?string $id, ?array $champs) : ?int{
        switch($table){
            case "" :
            default:
                return $this->updateOneTupleOneTable($table, $id, $champs);
        }
    }

    protected function traitementDelete(string $table, ?array $champs) : ?int{
        switch($table){
            case "" :
            default:
                return $this->deleteTuplesOneTable($table, $champs);
        }
    }

    private function selectTuplesOneTable(string $table, ?array $champs) : ?array{
        if(empty($champs)){
            $requete = "select * from $table;";
            return $this->conn->queryBDD($requete);
        }else{
            $requete = "select * from $table where ";
            foreach ($champs as $key => $value){
                $requete .= "$key=:$key and ";
            }
            $requete = substr($requete, 0, strlen($requete)-5);
            return $this->conn->queryBDD($requete, $champs);
        }
    }

    private function insertOneTupleOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "insert into $table (";
        foreach ($champs as $key => $value){
            $requete .= "$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ") values (";
        foreach ($champs as $key => $value){
            $requete .= ":$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);
        $requete .= ");";
        return $this->conn->updateBDD($requete, $champs);
    }

    private function updateOneTupleOneTable(string $table, ?string $id, ?array $champs) : ?int {
        if(empty($champs)){
            return null;
        }
        if(is_null($id)){
            return null;
        }
        $requete = "update $table set ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key,";
        }
        $requete = substr($requete, 0, strlen($requete)-1);
        $champs["id"] = $id;
        $requete .= " where id=:id;";
        return $this->conn->updateBDD($requete, $champs);
    }

    private function deleteTuplesOneTable(string $table, ?array $champs) : ?int{
        if(empty($champs)){
            return null;
        }
        $requete = "delete from $table where ";
        foreach ($champs as $key => $value){
            $requete .= "$key=:$key and ";
        }
        $requete = substr($requete, 0, strlen($requete)-5);
        return $this->conn->updateBDD($requete, $champs);
    }

    private function selectTableSimple(string $table) : ?array{
        $requete = "select * from $table order by libelle;";
        return $this->conn->queryBDD($requete);
    }

    private function selectAllLivres() : ?array{
        $requete = "Select l.id, l.ISBN, l.auteur, d.titre, d.image, l.collection, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from livre l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    private function selectAllDvd() : ?array{
        $requete = "Select l.id, l.duree, l.realisateur, d.titre, d.image, l.synopsis, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from dvd l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    private function selectAllRevues() : ?array{
        $requete = "Select l.id, l.periodicite, d.titre, d.image, l.delaiMiseADispo, ";
        $requete .= "d.idrayon, d.idpublic, d.idgenre, g.libelle as genre, p.libelle as lePublic, r.libelle as rayon ";
        $requete .= "from revue l join document d on l.id=d.id ";
        $requete .= "join genre g on g.id=d.idGenre ";
        $requete .= "join public p on p.id=d.idPublic ";
        $requete .= "join rayon r on r.id=d.idRayon ";
        $requete .= "order by titre ";
        return $this->conn->queryBDD($requete);
    }

    private function selectExemplairesRevue(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('id', $champs)){
            return null;
        }
        $champNecessaire['id'] = $champs['id'];
        $requete = "Select e.id, e.numero, e.dateAchat, e.photo, e.idEtat ";
        $requete .= "from exemplaire e join document d on e.id=d.id ";
        $requete .= "where e.id = :id ";
        $requete .= "order by e.dateAchat DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }

    /**
     * récupère toutes les commandes d'un livre ou DVD avec infos de suivi
     * @param array|null $champs (doit contenir 'idLivreDvd')
     * @return array|null
     */
    private function selectCommandesLivreDvd(?array $champs) : ?array{
        if(empty($champs)){
            return null;
        }
        if(!array_key_exists('idLivreDvd', $champs)){
            return null;
        }
        $champNecessaire['idLivreDvd'] = $champs['idLivreDvd'];
        $requete = "SELECT c.id, c.dateCommande, c.montant, cd.nbExemplaire, cd.idSuivi, s.libelle as suivi ";
        $requete .= "FROM commande c ";
        $requete .= "JOIN commandedocument cd ON c.id = cd.id ";
        $requete .= "JOIN suivi s ON cd.idSuivi = s.id ";
        $requete .= "WHERE cd.idLivreDvd = :idLivreDvd ";
        $requete .= "ORDER BY c.dateCommande DESC";
        return $this->conn->queryBDD($requete, $champNecessaire);
    }

}