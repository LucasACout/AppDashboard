$(document).ready(() => {
	
  $('#doc').on('click', () => {
    //
    $('#pagina').load('documentacao.html');
  });
  
  $('#sup').on('click', () => {
    //
    $.get('suporte.html', data => {

      $('#pagina').html(data);

    });
  });

  //

  $('#selectData').on('change', e => {

    let comp = $(e.target).val();

    $.ajax({
      type: 'GET',
      url: 'app.php', //
      data: `competencia=${comp}`, //
      dataType: 'json',
      success: dados => {
        $('#numVendas').html(dados.num_vendas);
        $('#totalVendas').html(dados.total_vendas);
        $('#clientesAtivos').html(dados.clientes_ativos);
        $('#clientesInativos').html(dados.clientes_inativos);
        $('#totalDespesas').html(dados.total_despesas);

      }, 
      error: erro => {console.log(erro)}
    });

  });
});