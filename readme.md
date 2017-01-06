![Frete Rápido - Sistema Inteligente de Gestão Logística](https://freterapido.com/imgs/frete_rapido.png)
===

### Módulo para plataforma 	WooCommerce
Versão do módulo: 1.0.0

Compatibilidade com WooCommerce: 2.6x

Links úteis:

- [Painel administrativo][2]
- [suporte@freterapido.com][3]

----------

### Instalação

>**ATENÇÃO!** Recomendamos que seja feito backup da sua loja antes de realizar qualquer instalação. A instalação desse módulo é de inteira responsabilidade do lojista.

----------
####Instalação manual

- [Baixe aqui a última versão][4], descompacte o conteúdo do arquivo zip dentro da "wp-content/plugins", ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

![Mensagem de atenção para backup da loja](http://freterapido.com/imgs/magento_doc/attention_2.png "#FicaDica ;)")

----------

### Configurações

É necessário realizar algumas configurações na sua loja para obter total usabilidade do módulo **Frete Rápido**.

- As informações sobre o remetente das mercadorias são muito importantes para sabermos qual a origem dos seus fretes. Acesse a área administrativa da sua loja e informe os dados de origem em: System > Settings > Shipping Settings > Origin.

![Sessão de dados da origem](https://freterapido.com/imgs/magento_doc/origin_settings.PNG "Dados de origem")


> **Obs:** É importante informar todos os campos corretamente.

- É necessário relacionar cada categoria da sua loja com as categorias do Frete Rápido em: Catalog > Manage Categories > Categoria no Frete Rápido.

![Configuração de categorias ](https://freterapido.com/imgs/magento_doc/categories_settings.PNG "Configuração de categorias")

> **Obs:** Nem todas as categorias da sua loja podem coincidir com a relação de categorias do Frete Rápido, mas é possível relacioná-las de forma ampla.
>
> **Exemplo 1**: Moda feminina -> Vestuário

> **Exemplo 2**: CDs -> CD / DVD / Blu-Ray

> **Exemplo 3**: Violões -> Instrumento Musical

- Para calcular o frete precisamos saber as medidas das embalagens de cada produto. Você precisa informá-las em: Catalog > Manage Products > Frete Rápido.

![Configurando as medidas das embalagens dos produtos](https://freterapido.com/imgs/magento_doc/iten_setting.PNG "Configuração de medidas dos produtos")

> **Obs:** As medidas devem considerar apenas as dimensões da embalagem pronta para envio/postagem.

#### Configurações do módulo:

- Agora, configure a nova forma de entrega: System > Settings > Shipping Methods > Frete Rápido (conforme imagem abaixo).

![Configurando o módulo do Frete Rápido](http://freterapido.com/imgs/magento_doc/extension_settings.PNG?v=1 "Configurações do módulo")

- **Habilitar:** Habilita ou desabilita o módulo conforme sua necessidade.

- **CNPJ:** CNPJ da sua empresa conforme registrado no Frete Rápido.

- **Inscrição Estadual:** Inscrição estadual da sua empresa, caso seja isento, informar “ISENTO”.

- **Formato de Peso:** Define o formato de peso utilizado pela loja em Quilos ou Gramas.

- **Correios - Valor Declarado:** Habilita ou desabilita o serviço de “Valor Declarado” com os Correios, definindo se os valores dos produtos serão declarados.

- **Correios - Mão Própria:** Habilita ou desabilita o serviço de “Mão Própria”.

- **Correios - Aviso de Recebimento:** Habilita ou desabilita o serviço de “Aviso de Recebimento”.

- **Resultados:** Define como deseja receber as cotações.

- **Limite:** Permitir limitar, até 20, a quantidade de cotações que deseja apresentar ao visitante.

- **Prazo adicional de envio/postagem (dias):** Permitir inserir a quantidade de dias necessário para despacho das mercadorias. Esse valor será acrescido ao prazo do frete.

- **Custo adicional de envio/postagem (R$):** Permite informar, caso haja, um custo adicional de despacho das mercadorias. Esse valor será acrescido ao valor do frete.

- **Token:** Token de integração da sua empresa disponível no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.

--------

###Observações gerais:
1. Para obter cotações dos Correios é necessário configurar o seu contrato com os Correios no [Painel administrativo do Frete Rápido][2] > Empresa > Integração.
2. Esse módulo atende cotações apenas para destinatários Pessoa Física.

--------

###Contribuições
Encontrou algum bug ou tem sugestões de melhorias no código? Sencacional! Não se acanhe, nos envie um pull request com a sua alteração e ajude este projeto a ficar ainda melhor.

1. Faça um "Fork"
2. Crie seu branch para a funcionalidade: ` $ git checkout -b feature/nova-funcionalidade`
3. Faça o commit suas modificações: ` $ git commit -am "adiciona nova funcionalidade"`
4. Faça o push para a branch: ` $ git push origin feature/nova-funcionalidade`
5. Crie um novo Pull Request

--------

### Licença
[MIT][5]


[1]: https://www.magentocommerce.com/magento-connect/catalogsearch/result/?q=frete+r%C3%A1pido&pl=0 "Magento Connect"
[2]: https://freterapido.com/painel/?origin=github_magento "Painel do Frete Rápido"
[3]: mailto:suporte@freterapido.com "E-mail para a galera super gente fina :)"
[4]: https://github.com/freterapido/freterapido_magento/archive/master.zip
[5]: https://github.com/freterapido/freterapido_magento/blob/master/LICENSE
