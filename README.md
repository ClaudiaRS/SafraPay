# SafraPay
Exemplo de integração com a API de pagamentos SafraPay

Gerar certificado

Durante o processo de homologação você receberá 4 certificados; 
una-os conforme a versão

Para homologação
cat Gateway_hml.cer Intermediate.cer Root.cer > client.cer

Ou para produção
cat Gateway_prd.cer Intermediate.cer Root.cer > client.cer


Depois, una o arquivo CSR com o KEY

Se uma aplicação é executada em ambiente Linux, gere um arquivo PEM

cat client.key client.cer > client.pem

Caso sua aplicação rode em ambiente Windows, gere um arquivo PFX
no formato PKCS#12

openssl pkcs12 -export -in client.cer -inkey client.key -out client.pfx

Deixe a senha de desafio em branco (pressione Enter duas vezes para confirmar).

