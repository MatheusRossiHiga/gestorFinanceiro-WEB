CREATE DATABASE sistemaFinanceiro;

USE sistemaFinanceiro;

CREATE TABLE usuarios (
    cd_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nm_usuario VARCHAR(200) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

CREATE TABLE Contas (
    cd_conta INT AUTO_INCREMENT PRIMARY KEY,
    nm_conta VARCHAR(50) NOT NULL,
    tp_conta VARCHAR(20) NOT NULL,
    vl_conta_atual DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(cd_usuario)
);

CREATE TABLE Categorias (
    cd_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nm_categoria VARCHAR(50) NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(cd_usuario)
);

CREATE TABLE Transacoes (
    cd_transacoes INT AUTO_INCREMENT PRIMARY KEY,
    ds_descricao VARCHAR(255) NOT NULL,
    vl_transacao DECIMAL(10,2) NOT NULL,
    tp_transacao CHAR(1) NOT NULL,
    dt_transacao DATETIME NOT NULL,
    st_transacao CHAR(1) NOT NULL DEFAULT 'C',
    id_conta INT NOT NULL,
    id_categoria INT NOT NULL,
    FOREIGN KEY (id_conta) REFERENCES Contas(cd_conta),
    FOREIGN KEY (id_categoria) REFERENCES Categorias(cd_categoria)
);

CREATE TABLE Metas (
    cd_meta INT AUTO_INCREMENT PRIMARY KEY,
    nm_meta VARCHAR(100) NOT NULL,
    vl_objetivo DECIMAL(10,2) NOT NULL,
    vl_guardado DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    dt_limite DATE,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(cd_usuario)
);
