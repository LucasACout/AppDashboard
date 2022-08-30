<?php

  class Dashboard{
    public $data_inicio;
    public $data_fim;
    public $num_vendas;
    public $total_vendas;
    public $clientes_ativos;
    public $clientes_inativos;
    public $total_despesas;

    public function __get($atributo){
      return $this->$atributo;
    }

    public function __set($atributo, $valor){
      $this->$atributo = $valor;
      return $this;
    }
  }

  class Conexao{
    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $user = 'root';
    private $pass = '';

    public function conectar(){
      try{

        //
        $conexao = new PDO(
          "mysql:host=$this->host;dbname=$this->dbname",
          "$this->user",
          "$this->pass"
        );

        //
        $conexao->exec('set charset set utf8');

        return $conexao;

      }catch(PDOException $e){
        echo '<p>'.$e->getMessage().'</p>';
      };
    }
  }

  //

  class Bd{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard){
      $this->conexao = $conexao->conectar();
      $this->dashboard = $dashboard;
    }

    public function getNumVendas(){
      $query = 'select count(*) as num_vendas from tb_vendas where data_venda between :data_inicio and :data_fim;';

      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->num_vendas;
    }

    public function getTotalVendas(){
      $query = 'select sum(total) as total_vendas from tb_vendas where data_venda between :data_inicio and :data_fim;';

      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getClientesAtivos(){
      $query = 'select count(*) as clientes_ativos from tb_clientes where cliente_ativo = 1';

      $stmt = $this->conexao->prepare($query);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
    }

    public function getClientesInativos(){
      $query = 'select count(*) as clientes_inativos from tb_clientes where cliente_ativo = 0';

      $stmt = $this->conexao->prepare($query);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
    }

    public function getTotalDespesas(){
      $query = 'select sum(total) as total_despesas from tb_despesas where data_despesa between :data_inicio and :data_fim;';

      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
    }
  }

  //
  $dashboard = new Dashboard();

  //
  $competencia = explode('-', $_GET['competencia']);
  $ano = $competencia[0];
  $mes = $competencia[1];

  //
  $dia_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

  //
  $dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
  $dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dia_do_mes);

  $conexao = new Conexao();

  $bd = new Bd($conexao, $dashboard);

  $dashboard->__set('num_vendas', $bd->getNumVendas());
  $dashboard->__set('total_vendas', $bd->getTotalVendas());
  $dashboard->__set('clientes_ativos', $bd->getClientesAtivos());
  $dashboard->__set('clientes_inativos', $bd->getClientesInativos());
  $dashboard->__set('total_despesas', $bd->getTotalDespesas());

  //
  echo json_encode($dashboard);

?>