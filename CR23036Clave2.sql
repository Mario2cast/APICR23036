CREATE DATABASE IF NOT EXISTS salud_cr23036;
USE salud_cr23036;

CREATE TABLE Hospitales (
    IdHospital VARCHAR(10) PRIMARY KEY,
    NomHospital VARCHAR(100) NOT NULL,
    CapacidadAtencion VARCHAR(50) NOT NULL,
    Especialidades VARCHAR(150) NOT NULL
);

CREATE TABLE Doctores (
    IdDoctor VARCHAR(10) PRIMARY KEY,
    NombresDoctor VARCHAR(80) NOT NULL,
    ApellidosDoctor VARCHAR(80) NOT NULL,
    Especialidad VARCHAR(100) NOT NULL,
    TurnoAtencion VARCHAR(50) NOT NULL,
    PacientesMinDiarios INT NOT NULL,
    Sueldo DOUBLE NOT NULL,
    IdHospital VARCHAR(10) NOT NULL,
    CONSTRAINT fk_doctor_hospital
        FOREIGN KEY (IdHospital)
        REFERENCES Hospitales(IdHospital)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

INSERT INTO Hospitales 
(IdHospital, NomHospital, CapacidadAtencion, Especialidades)
VALUES
('H001', 'Hospital Nacional Rosales', '500 pacientes', 'Medicina general, cirugía, cardiología'),
('H002', 'Hospital Nacional de Niños Benjamín Bloom', '300 pacientes', 'Pediatría, neonatología, cirugía pediátrica');

INSERT INTO Doctores 
(IdDoctor, NombresDoctor, ApellidosDoctor, Especialidad, TurnoAtencion, PacientesMinDiarios, Sueldo, IdHospital)
VALUES
('D001', 'Carlos Alberto', 'Ramírez López', 'Cardiología', 'Matutino', 15, 1200.50, 'H001'),
('D002', 'María Fernanda', 'Gómez Pérez', 'Pediatría', 'Vespertino', 20, 1150.75, 'H002');