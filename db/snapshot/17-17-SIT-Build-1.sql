 /*
 *
 * Created a new Table in the client schema {Client.GoMobileConfiguration_Tbl} to retain gomobile configuration
 * for every channel - CMP-1820
 *
 */
-- Table: client.gomobileconfiguration_tbl

-- DROP TABLE client.gomobileconfiguration_tbl;

CREATE TABLE client.gomobileconfiguration_tbl
(
  id serial NOT NULL,
  clientid integer NOT NULL,
  name character varying(100),
  value character varying(100),
  channel character varying(5),
  created timestamp without time zone DEFAULT now(),
  modified timestamp without time zone DEFAULT now(),
  enabled boolean DEFAULT true,
  CONSTRAINT gomobileconfiguration_pk PRIMARY KEY (id),
  CONSTRAINT gomobileconfiguration2client_fk FOREIGN KEY (clientid)
      REFERENCES client.client_tbl (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE client.gomobileconfiguration_tbl
  OWNER TO mpoint;
  
  
INSERT INTO client.gomobileconfiguration_tbl (clientid, name, value, channel) VALUES 
(10008, 'keyword', 'CPM', 'SMS'),
(10008, 'channel', '123', 'SMS'),
(10008, 'price', '0', 'SMS'),
(10008, 'keyword', 'CPM', 'PUSH'),
(10008, 'channel', '123', 'PUSH'),
(10008, 'sender', 'support@cellpointmobile.com', 'EMAIL');