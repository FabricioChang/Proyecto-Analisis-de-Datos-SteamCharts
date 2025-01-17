require 'open-uri'
require 'nokogiri'
require 'csv'
require 'fileutils'

# literal 2
class Juego
  attr_accessor :nombre, :datos

  def initialize(nombre, datos)
    @nombre = nombre
    @cantidad = datos.length
    @datos = datos
  end

  def guardar(archivo_csv)
    CSV.open(archivo_csv, 'w') do |csv|
      csv << [@nombre, 'Cantidad de datos:', @cantidad]
      csv << %w[fecha promedio maximo]
      @datos.each do |dato|
        csv << [dato.fecha, dato.promedio, dato.maximo]
      end
    end
  end
end

class WoW
  attr_accessor :nombre, :datos

  def initialize(nombre, datos)
    @nombre = nombre
    @cantidad = datos.length
    @datos = datos
  end

  def guardar(archivo_csv)
    CSV.open(archivo_csv, 'w') do |csv|
      csv << ['Nombre:', @nombre]
      csv << ['Cantidad de datos:', @cantidad]
      csv << %w[fecha promedio]
      @datos.each do |dato|
        csv << [dato.fecha, dato.promedio]
      end
    end
  end
end

class Dato
  attr_accessor :fecha, :promedio, :maximo

  def initialize(fecha, promedio, maximo)
    @fecha = fecha
    @promedio = promedio
    @maximo = maximo
  end
end

class DatoWoW
  attr_accessor :fecha, :promedio

  def initialize(fecha, promedio)
    @fecha = fecha
    @promedio = promedio
  end
end
# literal 1

class Scrapper
  def extraer(url)
    FileUtils.mkdir_p('datos')
    steamChart = URI.open(url)
    datos = steamChart.read
    parsed_content = Nokogiri::HTML(datos)
    nombre = parsed_content.css('#app-title a').inner_text.strip
    lista = []
    parsed_content.css('.odd').each do |dato|
      fecha = dato.css('td.month-cell.left').inner_text.strip
      promedio = dato.css('td.right.num-f').inner_text.strip
      maximo = dato.css('td.right.num').inner_text.strip
      dato = Dato.new(fecha, promedio, maximo)
      lista.push(dato)
    end
    juego = Juego.new(nombre, lista)
    ruta_csv = File.join('datos', "#{nombre}.csv")
    juego.guardar(ruta_csv)
  end
end

class ScrapperWoW
  def extraer(url)
    FileUtils.mkdir_p('datos')
    steamChart = URI.open(url)
    datos = steamChart.read
    parsed_content = Nokogiri::HTML(datos)
    nombre = parsed_content.css('h1').inner_text.strip
    lista = []
    tabla = parsed_content.css('table.wpDataTable.wpDataTableID-38 tbody')
    tabla.css('tr').each do |fila|
      fecha = fila.css('td:nth-child(1)').text.strip
      promedio = fila.css('td:nth-child(2)').text.strip
      next if fecha.nil? || promedio.nil?

      dato = DatoWoW.new(fecha, promedio)
      lista.push(dato)
    end
    juego = WoW.new(nombre, lista)
    ruta_csv = File.join('datos', "#{nombre}.csv")
    juego.guardar(ruta_csv)
  end
end

juegos = {
  'The Elder Scrolls Online' => '306130',
  'Final Fantasy XIV' => '39210',
  'Star Wars: The Old Republic' => '1286830',
  'Guild Wars 2' => '1284210',
  'RuneScape' => '1343400',
  'Old School RuneScape' => '1343370',
  'The Lord of the Rings Online' => '212500',
  'AdventureQuest 3D' => '429790',
  'Project Gorgon' => '342940',
  'Lost Ark' => '1599340',
  'Path of Exile' => '238960',
  'Path of Exile 2' => '2694490'
}

url = 'https://steamcharts.com/app/'
urlWow = 'https://activeplayer.io/world-of-warcraft/'

juegos.each do |_nombre, codigo|
  extractor = Scrapper.new
  extractor.extraer(url + codigo)
end

extractorWow = ScrapperWoW.new
extractorWow.extraer(urlWow)
