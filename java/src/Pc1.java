import com.mongodb.AggregationOutput;
import com.mongodb.BasicDBObject;
import com.mongodb.DB;
import com.mongodb.DBCollection;
import com.mongodb.DBObject;
import com.mongodb.MongoClient;
import com.mongodb.MongoClientOptions;
import com.mongodb.MongoClientURI;
import com.mongodb.MongoException;
import com.mongodb.MongoSocketOpenException;
import com.mongodb.client.FindIterable;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import com.mongodb.client.model.Accumulators;
import com.mongodb.client.model.Aggregates;
import com.mongodb.util.JSON;
import com.mongodb.util.JSONParseException;

import java.text.SimpleDateFormat;
import java.time.Instant;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.util.Arrays;
import java.util.Date;

import org.bson.Document;
import org.eclipse.paho.client.mqttv3.*;
import org.eclipse.paho.client.mqttv3.persist.MemoryPersistence;

public class Pc1 {
	 private static int medTemp1Id = 0;
	 private static int medTemp2Id = 0;
	 private static int medMovId = 0;
	 private static int anomaliasId =0 ;
	 
	 
	 //private static String topic1 = "pisid_mazetemp";
	 
	 private static String topic1 = Env.get("MQTT_TOPIC_TEMP", "pisid_grupo10_temp");
	 private static String topic2 = Env.get("MQTT_TOPIC_MAZE", "ratos");

	 
	 public static void main(String[] args) {
		    String broker = Env.get("MQTT_BROKER", "tcp://localhost:1883");
		    String clientId = Env.get("MQTT_CLIENT_ID", "pisid-pc1");

		    //String ipv4= "192.168.1.65";
		    String ipv4= "localhost";
		    
		    connectAndSubscribe(broker, clientId, ipv4);
	 }
	 
	 
	 
	 private static void connectAndSubscribe(String broker, String clientId, String ipv4) {
		    try {
		        // Configure MongoDB Replica Set connection
		        MongoClientURI uri = new MongoClientURI(Env.get("MONGO_URI", "mongodb://localhost:27019,localhost:25019,localhost:23019/?replicaSet=replicaPisid"));
		        MongoClient mongoClient = null;
		        try {
		            mongoClient = new MongoClient(uri);
		        } catch (MongoSocketOpenException e) {
		            System.out.println("Handled: Could not open socket to MongoDB. Check MongoDB connection settings.");
		        
		        }
		        if (mongoClient != null) {
		            // Connect to the database
		            DB database = mongoClient.getDB("experiencia");
		            DBCollection collection1 = database.getCollection("medicoesTemperaturas1");
		             DBCollection collection2 = database.getCollection("medicoesTemperaturas2");
		            DBCollection collection3 = database.getCollection("medicoesMovimentos");
		            DBCollection collection4 = database.getCollection("anomalia");

		            medTemp1Id = getLastId(collection1);
		            medTemp2Id = getLastId(collection2);
		            medMovId = getLastId(collection3);
		            anomaliasId = getLastId(collection4);
		            
		            MqttClient client = new MqttClient(broker, clientId, new MemoryPersistence());
		            MqttConnectOptions connOpts = new MqttConnectOptions();
		            connOpts.setCleanSession(true);

		            System.out.println("Connecting to broker: " + broker);
		            client.connect(connOpts);
		            System.out.println("Connected");
  
		            client.subscribe(topic1);
		            client.subscribe(topic2);

		            client.setCallback(new MqttCallback() {
		                @Override
		                public void connectionLost(Throwable cause) {
		                    System.out.println("Connection lost");
		                    connectAndSubscribe(broker, clientId, ipv4);
		                }

		                @Override
		                public void messageArrived(String topic, MqttMessage message) throws Exception {
		                    String messageString = new String(message.getPayload());
		                    String[] vector = {topic, messageString};
		                    System.out.println(topic + ": " + new String(message.getPayload()));
		                    uploadToMongo(database, vector);
		                }

		                @Override
		                public void deliveryComplete(IMqttDeliveryToken token) {
		                }
		            });

		            while (true) {
		                Thread.sleep(1000);
		            }
		        }

		    } catch (MqttException | InterruptedException e) {
		        e.printStackTrace();
		    }
		}
        
        
    private static void uploadToMongo(DB database, String[] message) {
        try {
        	
            BasicDBObject document = (BasicDBObject) JSON.parse(message[1]);
            
            int id1;
            int id2;
            int id3;
            int id4;
            
            if (message[0].equals(topic1)) {
            	
                String[] aux = message[1].split(", ");
                
                Boolean isAnomaly = hasValueTypeAnomalyForTemperatures(document);
                
                System.out.println("Anomlia:" + isAnomaly);
                
                if(isAnomaly) {
                	
                	uploadAnomalia(document , database);
                	System.out.println("Uploaded to collection anomalia: " + document.toJson());
                }
                if(!isAnomaly) {
                if (aux[2].equals("Sensor: 1}")) {
                	   id1 = ++medTemp1Id;
                	   document.append("incremental_id", id1);
                	   database.getCollection("medicoesTemperaturas1").insert(document);
                    System.out.println("Uploaded to collection medTemp1: " + document.toJson());
                }
                if (aux[2].equals("Sensor: 2}")) {
                	  id2 = ++medTemp2Id;
                	  document.append("incremental_id", id2);
          			database.getCollection("medicoesTemperaturas2").insert(document);
                    System.out.println("Uploaded to collection medTemp2: " + document.toJson());
                }
                }
            }
            if(message[0].equals(topic2)) {
            	Boolean isAnomaly = hasAnomaliesForRooms(document);
            	if(isAnomaly) {
            		
            		uploadAnomalia(document , database);
            		System.out.println("Uploaded to collection anomalia: " + document.toJson());
            	}
            	if(!isAnomaly) {
            	//System.out.println(isAnomaly);
            	id3 = ++medMovId;
         	   document.append("incremental_id", id3);
            	database.getCollection("medicoesMovimentos").insert(document);
            	System.out.println("Uploaded to collection medMov: " + document.toJson());
            } } 
        } catch (com.mongodb.util.JSONParseException e) {
           // System.out.println("Error uploading to MongoDB: " + e.getMessage());
            	 System.out.println("Anomalia de JSONParseException");
            	 BasicDBObject anomalyDocument = new BasicDBObject();
            	 String collection = getAnomalyCollection(message);
            	 anomalyDocument.append("Erro", "Erro de parsing");
            	 anomalyDocument.append("Hora", getCurrentTime());
            	 
            	uploadAnomalia(anomalyDocument , database);
            	
            	  
                  System.out.println(" Uploaded to collection anomalia: " + anomalyDocument.toJson());
         } catch (Exception e) {
            System.out.println("Error uploading to MongoDB: " + e.getMessage());
        }

    }
    
    private static boolean hasValueTypeAnomalyForTemperatures(BasicDBObject document) {
       
        if (!document.containsKey("Hora") || !document.containsKey("Leitura") || !document.containsKey("Sensor")) {
            return true; 
        }

       
        if (document.get("Hora") == null || document.get("Leitura") == null || document.get("Sensor") == null) {
            return true; 
        }

        
        if (!(document.get("Leitura") instanceof Double || document.get("Leitura") instanceof Integer)) {
            return true; 
        }
        
          double leituraValue = document.getDouble("Leitura");
           if (!(leituraValue < 100 && leituraValue > -100)) {
                return true;
            }
        
        
     
        if (!isValidTimestamp((String) document.get("Hora"))) {
            return true;
        }
        
        
        if (!(document.get("Sensor") instanceof Integer)) {
            return true; 
        } else {
        	  
            
            int sensor = document.getInt("Sensor");
            if (sensor != 1 && sensor != 2) {
                return true; 
            }
           
        }
       
        return false;
    }
    
    private static boolean hasAnomaliesForRooms(BasicDBObject document) {
        
        if (!document.containsKey("Hora") || !document.containsKey("SalaOrigem") || !document.containsKey("SalaDestino")) {
            return true; 
        }

       
        if (document.get("Hora") == null || document.get("SalaOrigem") == null || document.get("SalaDestino") == null) {
            return true; 
        }

   
        if (!isValidTimestamp((String) document.get("Hora"))) {
            return true; 
        }

        
        if (!(document.get("SalaOrigem") instanceof Integer) || !(document.get("SalaDestino") instanceof Integer)) {
            return true; 
        } 

       
        return false;
    }
    
  
    private static boolean isValidTimestamp(String timestamp) {
        
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss.SSSSSS");

        try {
            
            LocalDateTime dateTime = LocalDateTime.parse(timestamp, formatter);

           
            LocalDate currentDate = LocalDate.now();
            LocalDate timestampDate = dateTime.toLocalDate();
            
            return currentDate.equals(timestampDate);
        } catch (DateTimeParseException e) {
            
            return false;
        }
    }
    
    private static String getCurrentTime() {
       
        LocalDateTime currentTime = LocalDateTime.now();
        
      
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss.SSSSSS");
        
       
        String formattedTime = currentTime.format(formatter);
        
        return formattedTime;
    }
    
   private static String getAnomalyCollection (String[] message) {
	   String collection = "";
	   if (message[0].equals(topic1)) {
		   String[] aux = message[1].split(", ");
		   if (aux[2].equals("Sensor: 1}")) {
			   collection = "medicoesTemperaturas1";
			   return collection;
		   }
		   if (aux[2].equals("Sensor: 2}")) {
			   collection = "medicoesTemperaturas2";
			   return collection;
		   }
	   }
		   if(message[0].equals(topic2)) {
			   collection = "medicoesMovimentos";
			   return collection;
		   }
		  collection = "Sensor Invalido";
		  return collection;
   }
    
   
  private static void uploadAnomalia(BasicDBObject anomalydocument , DB database ) {
	  anomaliasId++;
	  anomalydocument.append("incremental_id", anomaliasId);
	  SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss.SSSSSS");
	  anomalydocument.append("current_time", formatter.format(new Date()));
	  database.getCollection("anomalia").insert(anomalydocument);
	  
	 }
	  
	  
  
        
    private static int getLastId(DBCollection collection) {
        DBObject groupFields = new BasicDBObject("_id", null);
        groupFields.put("ultimoAutoIncremento", new BasicDBObject("$max", "$incremental_id"));
        DBObject group = new BasicDBObject("$group", groupFields);

        AggregationOutput output = collection.aggregate(Arrays.asList(group));
        
        // Verificar se há algum resultado antes de chamar next()
        if (output.results().iterator().hasNext()) {
            DBObject result = output.results().iterator().next();
            
            if (result != null) {
                Object ultimoAutoIncrementoObj = result.get("ultimoAutoIncremento");
                if (ultimoAutoIncrementoObj instanceof Integer) {
                    int ultimoAutoIncremento = ((Integer) ultimoAutoIncrementoObj).intValue();
                    System.out.println("Último autoincremento: " + ultimoAutoIncremento);
                    return ultimoAutoIncremento;
                } else {
                    System.out.println("Tipo de dado do autoincremento não é um inteiro.");
                    return 0; 
                }
            } else {
                System.out.println("Nenhum incremental_id encontrado.");
                return 0; 
            }
        } else {
            System.out.println("A agregação não retornou resultados.");
            return 0; 
        }
    }

    
    
    
    
    
    
    

        
    
    
    
   
    
}
