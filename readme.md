![Frete Rápido - Sistema Inteligente de Gestão Logística](https://freterapido.com/imgs/frete_rapido.png)
===

### Módulo para plataforma WooCommerce

Versão do módulo: 1.0.0

Compatibilidade com WooCommerce: **2.6.x**

Links úteis:

- [Painel administrativo][2]
- [suporte@freterapido.com][3]

----------

### Instalação

>**ATENÇÃO!** Recomendamos que seja feito backup da sua loja antes de realizar qualquer instalação. A instalação desse módulo é de inteira responsabilidade do lojista.


- [Baixe aqui a última versão][4], descompacte o conteúdo do arquivo zip dentro da pasta "wp-content/plugins", ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

![Instalando o plugin](https://freterapido.com/dev/imgs/woocommerce_2.6_doc/freterapido/plugin_install.gif "Procedimento de Instalação")

![Mensagem de atenção para backup da loja](https://freterapido.com/dev/imgs/woocommerce_2.6_doc/attention_2.png "#FicaDica ;)")

----------

### Configurações

É necessário realizar algumas configurações na sua loja para obter total usabilidade do plugin **Frete Rápido**.

####1. Configurações do módulo:

- Agora, configure a nova forma de entrega: **WooCommerce** > **Configurações** > **Entrega** > **Frete Rápido** (conforme imagem abaixo).

![Configurando o módulo do Frete Rápido](https://freterapido.com/dev/imgs/woocommerce_2.6_doc/freterapido/configuracao_plugin.png "Configurações do módulo")

- **Habilitar/Desabilitar:** Habilita ou desabilita o módulo conforme sua necessidade.

- **CNPJ:** CNPJ da sua empresa conforme registrado no Frete Rápido.

- **Resultados:** Define como deseja receber as cotações.

- **Limite:** Permitir limitar, até 20, a quantidade de cotações que deseja apresentar ao visitante.

- **Prazo adicional de envio/postagem (dias):** Permitir inserir a quantidade de dias necessário para despacho das mercadorias. Esse valor será acrescido ao prazo do frete.

- **Custo adicional de envio/postagem (R$):** Permite informar, caso haja, um custo adicional de despacho das mercadorias. Esse valor será acrescido ao valor do frete.

- **Percentual adicional (%):** Permite informar, caso haja, uma porcentagem adicional de custos referentes à operação do frete. Esse valor será acrescido ao valor do frete.

- **Comprimento padrão (cm):** Define a comprimento padrão dos produtos que não tiverem altura informada.

- **Largura padrão (cm):** Define a largura padrão dos produtos que não tiverem altura informada.

- **Altura padrão (cm):** Define a altura padrão dos produtos que não tiverem altura informada.

- **Token:** Token de integração da sua empresa disponível no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.

####2. Medidas, peso e prazo:

- Para calcular o frete precisamos saber as medidas das embalagens de cada produto e peso. Você precisa informá-los nas configurações do seu produto.

> **Obs:** Você também pode configurar o prazo de fabricação do produto, caso haja. Ele será acrescido no prazo de entrega do frete.

![Configurando as medidas das embalagens e peso dos produtos](https://freterapido.com/dev/imgs/woocommerce_2.6_doc/freterapido/product_settings.gif "Configuração das informações dos produtos")

> **Atenção:** Considerar as dimensões e peso do produto com a embalagem pronta para envio/postagem.
> É obrigatório ter o peso configurado em cada produto para que seja possível cotar o frete de forma eficiente. As dimensões podem ficar em branco, e, neste caso, serão utilizadas as medidas padrões informadas na configuração do plugin.
> Nós recomendamos que cada produto tenha suas próprias configurações de peso e dimensões para que você tenha seu frete cotado com mais precisão.

####3. Categorias
- Cada categoria da sua loja precisa estar relacionada com as categorias do Frete Rápido. Você pode configurar isso em: **Produtos** > **Categorias**.

![Configuração de categorias ](https://freterapido.com/dev/imgs/woocommerce_2.6_doc/freterapido/categoria_edicao.png "Configuração de categorias")

> **Obs:** Nem todas as categorias da sua loja podem coincidir com a relação de categorias do Frete Rápido, mas é possível relacioná-las de forma ampla.

> **Exemplo 1**: Moda feminina -> Vestuário

> **Exemplo 2**: CDs -> CD / DVD / Blu-Ray

> **Exemplo 3**: Violões -> Instrumento Musical

--------

###Observações gerais:
1. Para obter cotações dos Correios é necessário configurar o seu contrato com os Correios no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.
2. Esse módulo atende cotações apenas para destinatários Pessoa Física.

----------

### Cálculo do frete na página do produto

Nós desenvolvemos um plugin específico para calcular o frete na página do produto. Para instalá-lo, basta acessar sua documentação em [freterapido_woocommerce_2.6_shipping_product_page][6].

--------

###Contribuições
Encontrou algum bug ou tem sugestões de melhorias no código? Sensacional! Não se acanhe, nos envie um *pull request* com a sua alteração e ajude este projeto a ficar ainda melhor.

1. Faça um "Fork"
2. Crie seu branch para a funcionalidade: ` $ git checkout -b feature/nova-funcionalidade`
3. Faça o commit suas modificações: ` $ git commit -am "adiciona nova funcionalidade"`
4. Faça o push para a branch: ` $ git push origin feature/nova-funcionalidade`
5. Crie um novo Pull Request

--------

### Licença
[MIT][5]


[2]: https://freterapido.com/painel/?origin=github_woocommerce_freterapido "Painel do Frete Rápido"
[3]: mailto:suporte@freterapido.com "E-mail para a galera super gente fina :)"
[4]: https://github.com/freterapido/freterapido_woocommerce_2.6/archive/master.zip
[5]: https://github.com/freterapido/freterapido_woocommerce/blob/master/LICENSE
[6]: https://github.com/freterapido/freterapido_woocommerce_2.6_shipping_product_page
