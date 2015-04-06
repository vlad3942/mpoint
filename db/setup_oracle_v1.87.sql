/* ========== MPOINT V1.84 START ========== */
UPDATE Client_Ownr.Client_Tbl SET identification = 7;

INSERT INTO Log_Ownr.State_Tbl (id, name, module, func) VALUES (210, 'Invalid Message Authentication Code (MAC)', 'Validate', 'valMAC');
/* ========== MPOINT V1.84 END ========== */

/* ========== MPOINT V1.85 START ========== */
INSERT INTO Log_Ownr.State_Tbl (id, name, module, func) VALUES (1999, 'Callback retried', 'Callback', 'SurePay');
/* ========== MPOINT V1.85 END ========== */

/* ========== MPOINT V1.86 START ========== */

/* ========== MPOINT V1.86 END ========== */

/* ========== MPOINT V1.87 START ========== */
INSERT INTO Log_Ownr.State_Tbl (id, name, module, func) VALUES (1007, 'PSP Payment Request', 'Authorize', 'authTicket');
INSERT INTO Log_Ownr.State_Tbl (id, name, module, func) VALUES (1008, 'PSP Payment Response', 'Authorize', 'authTicket');

UPDATE System_Ownr.PSPCurrency_Tbl SET name = 'BDT' WHERE id = 302;
/* ========== MPOINT V1.87 END ========== */
