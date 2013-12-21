import java.io.*;
import java.util.*;

public class mkindex {
	

	public static void main(String[] args) {
		if ((args==null)||(args.length==0)) {
			processDir(new File("../"));
		} else {
			System.out.println("Use: Java makeIndex");
		}
	}

	static String str="";
	static String left="30";
	static String middle="5";
	static String right="75";
	static String width="360";
	static String height="240";
	static boolean showid=true;
	static boolean showtrlogo=true;
	static boolean showicon=true;
	static boolean showzip=true;
	static boolean showtitle=true;
	static boolean showexpl=true;
	static boolean showarea=true;
	static boolean showlevel=true;
	static boolean showproject=true;


	static void processDir(File dir) {
		String ini=readAll(new File("ini.txt"));
		String end=readAll(new File("end.txt"));
		String cfg=readAll(new File("cfg.txt")).toLowerCase();
		left=boundedBy("izquierda=\"",cfg,"\"",left);	
		middle=boundedBy("mitad=\"",cfg,"\"",middle);	
		right=boundedBy("derecha=\"",cfg,"\"",right);	
		width=boundedBy("icono_ancho=\"",cfg,"\"",width);		
		height=boundedBy("icono_alto=\"",cfg,"\"",height);	
		showid=!"no".equals(boundedBy("id=\"",cfg,"\"","no"));	
		showtrlogo=!"no".equals(boundedBy("trlogo=\"",cfg,"\"","no"));	
		showicon=!"no".equals(boundedBy("icono=\"",cfg,"\"","no"));	
		showzip=!"no".equals(boundedBy("descargar=\"",cfg,"\"","no"));	
		showtitle=!"no".equals(boundedBy("titulo=\"",cfg,"\"","no"));	
		showexpl=!"no".equals(boundedBy("descripcion=\"",cfg,"\"","no"));	
		showarea=!"no".equals(boundedBy("area=\"",cfg,"\"","no"));	
		showlevel=!"no".equals(boundedBy("nivel=\"",cfg,"\"","no"));	
		showproject=!"no".equals(boundedBy("proyecto=\"",cfg,"\"","no"));	
		File[] files=dir.listFiles();
		for (int i=0;i<files.length;i++) {
			if (files[i].isDirectory()) {
				addUnit(files[i]);
			}
		}
		writeInFile(ini+str+end,"../index.html");
	}
	
	static void addUnit(File dir) {
		String dirname=dir.getName();
		File index=new File("../"+dirname+"/index.html");
		if (index.exists()) {
			File noindex=new File("../"+dirname+"/_dontindex_");
			if (!noindex.exists()) {
					String strIndex=readAll(index);
					String title=boundedBy("<title>",strIndex,"</title>","Abrir");
					String expl=boundedBy("<descripcion>",strIndex,"</descripcion>","");

					String strmeta=strIndex;
					try {
						strmeta=readAll(new File("../"+dirname+"/meta/info.xml"));
						expl=boundedBy("<descripcion><![CDATA[",strmeta,"]]></descripcion>",expl);
						title=boundedBy("<titulo><![CDATA[",strmeta,"]]></titulo>",expl);
					} catch (Exception e) {
						strmeta=strIndex;
					}
					String nivel=boundedBy("nivel=\"",strmeta,"\"","");
					String area=boundedBy("area=\"",strmeta,"\"","");
					String proyecto=boundedBy("proyecto=\"",strmeta,"\"","");
					String proyecto_url=boundedBy("proyecto_url=\"",strmeta,"\"","");

					String icon=dirname+"/images/icono.png";
					File icono=new File("../"+icon);
					if (!icono.exists()) {
						icon=dirname+"/images/icono.jpg";
						icono=new File("../"+icon);
					}
					if (!icono.exists()) {
						icon=dirname+"/escenas/iconos/icono_01.jpg";
						icono=new File("../"+icon);
					}
					if (!icono.exists()) {
						icon=dirname+"/escenas/iconos/icono_01.png";
						icono=new File("../"+icon);
					}
					if (!icono.exists()) {
						icon="http://arquimedes.matem.unam.mx/images/endesarrollo.png";
					}
					if (proyecto.length()==0) {
						proyecto=proyecto_url;
					}

					str=str+""+"\r\n";
					str=str+"<tr><td><br/></td></tr>"+"\r\n";
					str=str+"<tr>"+"\r\n";
					str=str+"<td width=\""+left+"%\" align=\"center\">"+"\r\n";
					str=str+"<a href=\""+dirname+"/index.html\">"+"\r\n";
					if (showid) {
						str=str+"<small>"+dirname+"</small><br/>"+"\r\n";
					}
					if (showicon) {
						str=str+"<img src=\""+icon+"\" width=\""+width+"\" height=\""+height+"\">"+"\r\n";
					}
					if (showtrlogo) {
						str=str+"<img src=\"images/trlogo.png\">"+"\r\n";
					}
					str=str+"</a>"+"\r\n";
					if (showzip) {
						File zip=new File("../"+dirname+"/"+dirname+".zip");
						if (zip.exists()) {
							str=str+"<br/><a href=\""+dirname+"/"+dirname+".zip\"><i>Descargar</i></a>"+"\r\n";
						}
					}
					str=str+"</td>"+"\r\n";
					str=str+"<td width=\""+middle+"%\" align=\"left\"></td>"+"\r\n";
					str=str+"<td width=\""+right+"%\" align=\"left\">"+"\r\n";
					if (showtitle) {
						str=str+"<a href=\""+dirname+"/index.html\"><big>"+title+"</big></a>"+"\r\n";
						str=str+"<br/><br/>"+"\r\n";
					}
					if (showexpl) {
						str=str+expl+"\r\n";
						str=str+"<br/><br/>"+"\r\n";
					}
					if (showarea && (area.length()>0)) {
						str=str+"<strong>&Aacute;rea:</strong> "+area+"\r\n";
						str=str+"<br/>"+"\r\n";
					}
					if (showlevel && (nivel.length()>0)) {
						str=str+"<strong>Nivel:</strong> "+nivel+"\r\n";
						str=str+"<br/>"+"\r\n";
					}
					if (showproject && (proyecto.length()>0)) {
						str=str+"<br/>"+"\r\n";
						str=str+"<strong>Proyecto:</strong> ";
						if (proyecto_url.length()>0) {
							str=str+"<a href=\""+proyecto_url+"\">"+"\r\n";
						}
						str=str+proyecto+"\r\n";
						if (proyecto_url.length()>0) {
							str=str+"</a>"+"\r\n";
						}
						str=str+"<br/>"+"\r\n";
					}
					str=str+"</td>"+"\r\n";
					str=str+"</tr>"+"\r\n";
			}
		}
	}
	
	static String boundedBy(String bnd1,String str,String bnd2,String dflt) {
		int sz1=bnd1.length();
		int ix1= (str.toLowerCase().indexOf(bnd1.toLowerCase()));
		if (ix1>=0) {
			int ix2= (str.substring(ix1+sz1).toLowerCase().indexOf(bnd2.toLowerCase()));
			if ((ix1>=0) && (ix2>0)) {
				return str.substring(ix1+sz1,ix1+sz1+ix2);
			}
		}
		return dflt;
	}
	
	static String readAll(File f) {
		try {
			BufferedReader BR= new BufferedReader(new FileReader(f));;
			Vector V=new Vector();
			do {
				String str=BR.readLine();
				if (str==null) {
					break;
				}
				V.addElement(str);
			} while (true);
			BR.close();
			String content="";
			for (int i=0;i<V.size();i++) {
				if (i>0) {
					content+="\r\n";
				}
				content+=(String) V.elementAt(i);
			}
			return content;
		} catch (Exception e) {
			e.printStackTrace();
		}
		return null;
	}
	
	static void writeInFile(String content,String newfilestr) {
		try {
			newfilestr=newfilestr.replace("/",File.separator);
			newfilestr=newfilestr.replace("\\",File.separator);
			File newfile=new File(newfilestr);
			newfile.mkdirs();
			if (newfile.exists()) {
				newfile.delete();
			}
			BufferedWriter BW=new BufferedWriter(new FileWriter(newfile));
			BW.write(content,0,content.length());
			BW.flush();
			BW.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}	

	
}