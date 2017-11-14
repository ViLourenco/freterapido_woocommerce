![Frete Rápido - Sistema Inteligente de Gestão Logística](https://freterapido.com/imgs/frete_rapido.png)
<p>
    <a href="https://fr-slack.herokuapp.com" target="_blank">
        <img src="https://fr-slack.herokuapp.com/badge.svg">
    </a>
</p>
<hr>

### Módulo para plataforma WooCommerce

Versão do módulo: 1.0.3

Compatibilidade com WooCommerce: **2.6.x** ao **3.2.x**

Links úteis:

- [Painel administrativo][2]
- [suporte@freterapido.com][3]

---------------

### IMPORTANTE
A Frete Rápido não mantem e/ou oferece suporte para a integração com o **WooCommerce**, apenas disponibilizamos um módulo de integração padrão que atende a maioria dos e-commerces que utilizam esta plataforma.

Para qualquer modificação no módulo baseada no modelo de negócio do seu e-commerce, nós disponibilizamos aqui o código fonte do módulo integrado a [API do Frete Rápido][8] e aqui [neste link][7] você encontra a documentação de integrações e modificações do **WooCommerce**.

---------------

### Plugin adicional

Para o correto funcionamento do módulo do Frete Rápido é **obrigatória** a utilização do plugin [WooCommerce Extra Checkout Fields for Brazil][9] para que sejam informados os campos adicionais do destinatário durante o checkout, como CPF, RG, CNPJ e Inscrição Estadual.
A instalação é simples, basta seguir os passos no seu painel WordPress:  

1. Acesse o menu Plugins > Adicionar novo
2. No campo de pesquisa procure por WooCommerce Extra Checkout Fields for Brazil
3. Quando aparecer o plugin pesquisado clique em Instalar e depois em Ativar
4. Depois de instalado, basta acessar o menu WooCommerce > Campos do checkout e realizar as configurações do plugin
    
Para mais informações você pode visitar o link do plugin no [WooCommerce.org][9] ou [Github][10].

---------------

### Instalação

>**ATENÇÃO!** Recomendamos que seja feito backup da sua loja antes de realizar qualquer instalação. A instalação desse módulo é de inteira responsabilidade do lojista.


- [Baixe aqui a última versão][4], descompacte o conteúdo do arquivo zip dentro da pasta "wp-content/plugins", ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

![Instalando o plugin](docs/img/plugin_install.gif "Procedimento de Instalação")

![Mensagem de atenção para backup da loja](docs/img/attention_2.png "#FicaDica ;)")

---------------

### Configurações

É necessário realizar algumas configurações na sua loja para obter total usabilidade do plugin **Frete Rápido**.

#### 1. Configurações do módulo:

- Agora, configure a nova forma de entrega: **WooCommerce** > **Configurações** > **Entrega** > **Frete Rápido** (conforme imagem abaixo).

![Configurando o módulo do Frete Rápido](docs/img/module_page.png "Configurações do módulo")

- **Habilitar/Desabilitar:** Habilita ou desabilita o módulo conforme sua necessidade.
- **CNPJ:** CNPJ da sua empresa conforme registrado no Frete Rápido.
- **Resultados:** Define como deseja receber as cotações.
- **Limite:** Permitir limitar, até 20, a quantidade de cotações que deseja apresentar ao visitante.
- **Exibir Frete Grátis no frete mais barato:** Apresenta para o cliente da loja o frete mais barato como **Frete Grátis**.
- **Valor Mínimo Frete Grátis:**  Define o valor mínimo para ativar a regra de **Frete grátis**. Para valor **indefinido**, informe **0**.
- **Comprimento padrão (cm):** Define a comprimento padrão dos produtos que não tiverem altura informada.
- **Largura padrão (cm):** Define a largura padrão dos produtos que não tiverem altura informada.
- **Altura padrão (cm):** Define a altura padrão dos produtos que não tiverem altura informada.
- **Token:** Token de integração da sua empresa disponível no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.

#### 2. Medidas, peso e prazo:

- Para calcular o frete precisamos saber as medidas das embalagens de cada produto e peso. Você precisa informá-los nas configurações do seu produto.

> **Obs:** Você também pode configurar o prazo de fabricação do produto, caso haja. Ele será acrescido no prazo de entrega do frete.

![Configurando as medidas das embalagens e peso dos produtos](docs/img/product_settings.gif "Configuração das informações dos produtos")

> **Atenção:** Considerar as dimensões e peso do produto com a embalagem pronta para envio/postagem.
> É obrigatório ter o peso configurado em cada produto para que seja possível cotar o frete de forma eficiente. As dimensões podem ficar em branco, e, neste caso, serão utilizadas as medidas padrões informadas na configuração do plugin.
> Nós recomendamos que cada produto tenha suas próprias configurações de peso e dimensões para que você tenha seu frete cotado com mais precisão.

#### 3. Categorias
- Cada categoria da sua loja precisa estar relacionada com as categorias do Frete Rápido. Você pode configurar isso em: **Produtos** > **Categorias**.

![Configuração de categorias ](docs/img/categoria_edicao.png "Configuração de categorias")

> **Obs:** Nem todas as categorias da sua loja podem coincidir com a relação de categorias do Frete Rápido, mas é possível relacioná-las de forma ampla.

> **Exemplo 1**: Moda feminina -> Vestuário

> **Exemplo 2**: CDs -> CD / DVD / Blu-Ray

> **Exemplo 3**: Violões -> Instrumento Musical

---------------

### Observações gerais:
1. Para obter cotações dos Correios é necessário configurar o seu contrato com os Correios no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.
2. Esse módulo atende cotações apenas para destinatários Pessoa Física.

---------------

### Cálculo do frete na página do produto

Para cálculo do frete na página do produto, você precisa utilizar o plugin específico do Frete Rápido. Para instalá-lo, basta acessar sua documentação em [freterapido_woocommerce_2.6_shipping_product_page][6].

---------------

### Contratação do Frete

É possível contratar o frete diretamente na área administrativa da loja, no detalhamento do pedido do cliente.

* Abra o pedido em WooCommerce > Pedidos (1) e clique Editar (2) ou Visualizar (3).
![Caminho para contratar o frete](docs/img/pedidos.png "Detalhamento do pedido")

* Ao alterar o status para ***"À espera do envio"*** o frete é contratado automaticamente.
![Contratar o frete](docs/img/pedido_sem_frete_contratado.png "Contratando o frete")

* Após contratar, é disponibilizado o identificador do frete que leva para a página de rastreio.
![Frete contratado](docs/img/pedido_frete_contratado.png "Frete contratado")

---------------

### Contribuições
Encontrou algum bug ou tem sugestões de melhorias no código? Sensacional! Não se acanhe, nos envie um *pull request* com a sua alteração e ajude este projeto a ficar ainda melhor.

1. Faça um "Fork"
2. Crie seu branch para a funcionalidade: ` $ git checkout -b feature/nova-funcionalidade`
3. Faça o commit suas modificações: ` $ git commit -am "adiciona nova funcionalidade"`
4. Faça o push para a branch: ` $ git push origin feature/nova-funcionalidade`
5. Crie um novo Pull Request

---------------

### Licença
[MIT][5]


[2]: https://freterapido.com/painel/?origin=github_woocommerce_freterapido "Painel do Frete Rápido"
[3]: mailto:suporte@freterapido.com "E-mail para a galera super gente fina :)"
[4]: https://github.com/freterapido/freterapido_woocommerce/archive/master.zip
[5]: https://github.com/freterapido/freterapido_woocommerce/blob/master/LICENSE
[6]: https://github.com/freterapido/freterapido_woocommerce_2.6_shipping_product_page
[7]: https://woocommerce.com/developers/
[8]: https://www.freterapido.com/dev/
[9]: https://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/
[10]: https://github.com/claudiosanches/woocommerce-extra-checkout-fields-for-brazil
