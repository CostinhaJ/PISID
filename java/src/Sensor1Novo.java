import com.mongodb.MongoClient;
import com.mongodb.MongoClientURI;
import com.mongodb.MongoException;
import com.mongodb.client.FindIterable;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoCursor;
import com.mongodb.client.MongoDatabase;
import com.mongodb.client.model.Sorts;
import com.mongodb.client.model.changestream.ChangeStreamDocument;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.SQLNonTransientConnectionException;
import java.sql.Statement;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.time.temporal.ChronoUnit;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.concurrent.TimeUnit;

import org.bson.Document;



public class Sensor1Novo {

	 private static boolean connected = false;
	 private static double variacaoOutliers = 3; 
	 private static List<Double> temperatures = new ArrayList<>();
	 private static int IDExperiencia = 0;
	 
	 public static void main(String[] args) {
		
		  //String ipv4= "192.168.1.65";
		 String ipv4= "localhost";
	        // Connect to MongoDB server
	        try {
	        	  MongoClientURI uri = new MongoClientURI(Env.get("MONGO_URI", "mongodb://localhost:27019,localhost:25019,localhost:23019/?replicaSet=replicaPisid"));
	            MongoClient mongoClient = new MongoClient(uri);

	            // Connect to the database
	            MongoDatabase database = mongoClient.getDatabase("experiencia");
	            MongoCollection<Document> collection = database.getCollection("medicoesTemperaturas1");
	           
	           // Recovery: create the state document only once, so that all migrators can start.
	           if (database.getCollection("lastProcessedId").count() == 0) {
	               database.getCollection("lastProcessedId").insertOne(new Document());
	           }
	            
	            
	          
	            
	           int lastProcessedId = getLastProcessedIdFromMongoDB(database, "Sensor1");
	        
	            
	            
	            String url = Env.get("MYSQL_URL", "jdbc:mariadb://localhost:3306/pisid");
                String user = Env.get("MYSQL_USER", "java1");
                String password = Env.get("MYSQL_PASSWORD", "abc");
	            //collection2.drop();
	            Connection connection = connectSql(url, user, password);
	            
	            
	                
	                
	           
	            while (connected) {
	             desconexao(connection);
	            try {
	                try (MongoCursor<Document> cursor = collection.find().iterator()) {
	                    while (cursor.hasNext()) {
	                        Document document = cursor.next();
	                        
	                        
	                        int currentId = document.getInteger("incremental_id");
	                        if(currentId > lastProcessedId) {
	                        	System.out.println("Change occurred: " + document.toJson());
	                        	
	                        	
	                	        Object leituraObject = document.get("Leitura"); 
	                	        double currentleitura =0;
	                	        if (leituraObject instanceof Integer) {   
	                	            currentleitura = ((Integer) leituraObject).doubleValue();      
	                	        } else if (leituraObject instanceof Double) {	                	      
	                	            currentleitura = (Double) leituraObject;
	                	        }
	                        		
	                        	
	                        	
	                        	
	                	        //System.out.println("Temperature Size= "+ temperatures.size());
	                	        getVariacaoOutlier(connection);
	                        	boolean isOutlier = calculateMovingAverage(temperatures, currentleitura);
	                        	System.out.println("IsOutlier: " + isOutlier);
	                        	lastProcessedId = currentId;
	                        	temperatures.add(currentleitura);
	                        	
	                        	
	                        	updateLastProcessedIdInMongoDB(database,lastProcessedId, "Sensor1");
	                        	
	                        	insertDataIntoMariaDB(document, connection, isOutlier);
	                        	
	                        } else {
	                        	//System.out.println("Skipping already processed document with ID: " + currentId);
	                        }
	                    }
	                } catch (MongoException e) {
	                    e.printStackTrace();
	                }

	                // Sleep for some time before querying again
	                try {
	                    Thread.sleep(1000); 
	                } catch (InterruptedException e) {
	                    e.printStackTrace();
	                }
	                
	            }catch (SQLNonTransientConnectionException e) {
	            	
	            		System.err.println("!!!!!Connection Lost!!!!");
	            		connected = false;
                        connection = connectSql(url, user, password);
	            		//e.printStackTrace(); 
            	
            

	            } catch (SQLException e) {	
	            		
	            			e.printStackTrace();
	                        System.err.println("Connection Lost");
	                        connected = false;
	                        connection = connectSql(url, user, password);
	                    }
	                    	
	                    
	              
	            }
	        
	    

	   

	            mongoClient.close();

	        } catch (Exception e) {
	            e.printStackTrace();
	        }
	        
	        
	    } 
	    
	
	
	
	
	
	
	
	
	 private static void insertDataIntoMariaDB(Document document, Connection connection, Boolean outlier) throws Exception {
	        String hora = document.getString("Hora");
	        Object leituraObject = document.get("Leitura"); 
	        double leitura;
	        
	        
	        if (leituraObject instanceof Integer) {
	            
	            leitura = ((Integer) leituraObject).doubleValue();
	            
	            
	        } else if (leituraObject instanceof Double) {
	       
	            leitura = (Double) leituraObject;
	        } else {
	            
	            throw new IllegalArgumentException("Tipo de dado inválido para 'Leitura'");
	        }
	        int sensorID = document.getInteger("Sensor");
	        
	        boolean isOutlier = outlier;

	        String insertQuery = "INSERT INTO medicoestemperatura (SensorID, Hora, Leitura, isOutlier, IDExperiencia) VALUES (?, ?, ?, ?, NULL)";
	        PreparedStatement insertStatement = connection.prepareStatement(insertQuery);
	        insertStatement.setInt(1, sensorID);
	        java.sql.Timestamp timestamp = java.sql.Timestamp.valueOf(hora);
	        insertStatement.setTimestamp(2, timestamp);
	        insertStatement.setDouble(3, leitura);
	        insertStatement.setBoolean(4, isOutlier);
	  

	        int rowsAffected = insertStatement.executeUpdate();

	        if (rowsAffected == 1) {
	        	
	            System.out.println("Data inserted successfully into MariaDB! SensorID: " + sensorID + ", Hora: " + hora);
	            
	            
	        } else {
	            System.err.println("Error inserting data! Rows affected: " + rowsAffected);
	        }

	        insertStatement.close();
	    }

	    
	 private static int getLastProcessedIdFromMongoDB(MongoDatabase database, String sensorName) {
		    try {
		        MongoCollection<Document> collection = database.getCollection("lastProcessedId");
		        Document doc = collection.find().first();
		        if (doc != null) {
		            int lastProcessedId = 0;
		            if (sensorName.equals("Sensor1")) {
		                lastProcessedId = doc.getInteger("lastProcessedIdSensor1", 0);
		            } else if (sensorName.equals("Sensor2")) {
		                lastProcessedId = doc.getInteger("lastProcessedIdSensor2", 0);
		            }
		            System.out.println("Last processed ID for " + sensorName + ": " + lastProcessedId);
		            return lastProcessedId;
		        } else {
		            System.out.println("No document found in lastProcessedId collection");
		            return 0;
		        }
		    } catch (MongoException e) {
		        e.printStackTrace();
		        return 0;
		    }
		}

		private static void updateLastProcessedIdInMongoDB(MongoDatabase database, int lastProcessedId, String sensorName) {
		    try {
		        MongoCollection<Document> collection = database.getCollection("lastProcessedId");
		        Document filter = new Document();
		        String fieldName = sensorName.equals("Sensor1") ? "lastProcessedIdSensor1" : "lastProcessedIdSensor2";
		        Document update = new Document("$set", new Document(fieldName, lastProcessedId));
		        collection.updateOne(filter, update);
		        System.out.println("Last processed ID for " + sensorName + " updated to: " + lastProcessedId);
		    } catch (MongoException e) {
		        e.printStackTrace();
		    }
		}
	
	   
	 private static Connection connectSql(String url, String username, String password) throws InterruptedException {
	        Connection connection = null;
	        while(true) {
	        try {
	            connection = DriverManager.getConnection(url, username, password);
	            System.out.println("Connected to MariaDB");
	            connected = true;
	            break;
	        } catch (SQLException e) {
	        	System.err.println("Failed to connect to MariaDB. Retrying in 1 seconds...");
	        	//e.printStackTrace();
                Thread.sleep(1000); 
                connected= false;
	        }
	        }
	        return connection;
	    }
	
	
	
	 public static boolean calculateMovingAverage(List<Double> temperatures, Double currentTemperature) {
		    double average = 0;
		   
		    
		    if (temperatures.size() < 3) {
		        for (Double t : temperatures) {
		            System.out.println(t);
		        }
		        return false;
		    }

		    List<Double> lastFiveTemperatures = new ArrayList<>();
		    if (temperatures.size() >= 5) {
		        lastFiveTemperatures.addAll(temperatures.subList(temperatures.size() - 5, temperatures.size()));
		    } else {
		        lastFiveTemperatures.addAll(temperatures);
		    }

		    for (Double t : lastFiveTemperatures) {
		        System.out.println(t);
		    }
		    System.out.println("Last " + lastFiveTemperatures.size() + " readings");

		    // Remove outliers
		    lastFiveTemperatures.remove(Collections.max(lastFiveTemperatures));
		    lastFiveTemperatures.remove(Collections.min(lastFiveTemperatures));

		    double sum = 0;
		    for (double temperature : lastFiveTemperatures) {
		        sum += temperature;
		    }
		    average = sum / lastFiveTemperatures.size();

		    double lowerBound = average - variacaoOutliers;
		    double upperBound = average + variacaoOutliers;

		    return currentTemperature < lowerBound || currentTemperature > upperBound;
		}

	 
	private static void getVariacaoOutlier(Connection connection) {
		try {	
			 Statement statement = connection.createStatement();
	         ResultSet resultSet = statement.executeQuery(" SELECT IDExperiencia FROM `experiencia` WHERE Estado = 'ADecorrer' ");
		            if (resultSet.next()) {
		                IDExperiencia = resultSet.getInt("IDExperiencia");
		                System.out.println("Experiencia a decorrer: " + IDExperiencia);
		                Statement statement2 = connection.createStatement();
		   	         	ResultSet resultSet2 = statement2.executeQuery("SELECT VariacaoPermitidaOutliers FROM `experiencia` WHERE Estado = 'ADecorrer' AND IDexperiencia = " + IDExperiencia);
		   	         	if(resultSet2.next()) {
		   	         		variacaoOutliers = resultSet2.getDouble("VariacaoPermitidaOutliers");
		   	         		System.out.println("VariacaoOutlier = " + variacaoOutliers);
		   	         	} 	          
		            } else {
		            	System.out.println("Nao ha experiencia a decorrer");
		            	variacaoOutliers = 3;
		            	System.out.println("VariacaoOutlier = " + variacaoOutliers);
		            }
		            
		 } catch (SQLException e) {
			 e.printStackTrace();
		 }
		
	}
	
	
	
	
	
	
	
	
	private static boolean isExperienciaADecorrer(Connection connection) {
		try {
		 Statement statement = connection.createStatement();
         ResultSet resultSet = statement.executeQuery(" SELECT IDExperiencia FROM `experiencia` WHERE Estado = 'ADecorrer' ");
	   if (resultSet.next()) {
		   return true;
		   
	   } else { 
		   return false;
	   }
	 } catch (SQLException e) {
			 e.printStackTrace();
			 return false;
		 }
		
	}
	
	private static void desconexao(Connection connection) {	
		try {
			  int idexperiencia = 0;
		
			int tempoConectividade = 120;
			
				if(isExperienciaADecorrer(connection)){
					Statement statement = connection.createStatement();
			         ResultSet resultSet = statement.executeQuery(" SELECT IDExperiencia, TempoConectividade FROM `experiencia` WHERE Estado = 'ADecorrer' ");
			         resultSet.next();
			         tempoConectividade =  resultSet.getInt("TempoConectividade");
			         idexperiencia =  resultSet.getInt("IDExperiencia");
										
				}
					 Statement statement2 = connection.createStatement();			
					  ResultSet resultSet2 = statement2.executeQuery(" SELECT Hora FROM `medicoesTemperatura` WHERE SensorID = 1 ORDER BY Hora DESC LIMIT 1");
				         
			            if (resultSet2.next()) {
			               	Timestamp horaUltimaMedicao = resultSet2.getTimestamp("Hora");
			                LocalDateTime horaAtual = LocalDateTime.now();
			                LocalDateTime horaUltimaMedicaoLocal = horaUltimaMedicao.toLocalDateTime();
	
			                
			                long diferencaSegundos = ChronoUnit.SECONDS.between(horaUltimaMedicaoLocal, horaAtual);
			                System.out.println("Segundos sem resposta: " + diferencaSegundos);
			                if (diferencaSegundos > tempoConectividade ) {
			                    insertAlertaDesconexao(connection , idexperiencia);
			                    System.out.println("Alerta: Tempo de conectividade excedido!");
			                } else {
			                    System.out.println("Tempo de conectividade dentro do limite.");
			                
			                }
			            }

	 		 } catch (SQLException e) {
	 			 e.printStackTrace();
	 		 }
					       	            
		}
	
	
	
	public static void insertAlertaDesconexao(Connection connection , int id) {
		if (id  == 0 )
		{
			
			String sql = "INSERT INTO Alerta (TipoAlerta, Hora, Mensagem, HoraEscrita, Sala, Sensor, Leitura, IDExperiencia) " +
	                "VALUES (?, CURRENT_TIMESTAMP(), ?, NULL, NULL, NULL, NULL, NULL)";
	   
			
	   try ( PreparedStatement pstmt = connection.prepareStatement(sql)) {
	       
	       String tipoAlerta = "Conectividade";
	       String mensagem =  "Tempo de conectividade excedido!";
	       
	       pstmt.setString(1, tipoAlerta);
	       pstmt.setString(2, mensagem);
	     
	       
	       // Executa a consulta
	       pstmt.executeUpdate();
	       
	       System.out.println("Alerta inserido com sucesso!");
	       
	   } catch (SQLException e) {
	       System.out.println("Erro ao inserir o alerta: " + e.getMessage());
	   }
			
			
			
		} else {
			String sql = "INSERT INTO Alerta (TipoAlerta, Hora, Mensagem, HoraEscrita, Sala, Sensor, Leitura, IDExperiencia) " +
	                "VALUES (?, CURRENT_TIMESTAMP(), ?, NULL, NULL, NULL, NULL, ?)";
	   
			
	   try ( PreparedStatement pstmt = connection.prepareStatement(sql)) {
	       
	       String tipoAlerta = "Conectividade";
	       String mensagem =  "Tempo de conectividade excedido Sensor1!";
	       
	       pstmt.setString(1, tipoAlerta);
	       pstmt.setString(2, mensagem);
	       pstmt.setInt(3, id);
	       
	       // Executa a consulta
	       pstmt.executeUpdate();
	       
	       System.out.println("Alerta inserido com sucesso!");
	       
	   } catch (SQLException e) {
	     //  System.out.println("Erro ao inserir o alerta: " + e.getMessage());
		   
	   }

	}
	
	}
		
	
	
	
	
	
	
}
