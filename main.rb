# main.rb

# Cargar el archivo Scrapper.rb
require_relative 'Scrapper'

# URLs de los juegos y WoW
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
  'Path of Exile 2' => '2694490',
  'Throne and Liberty' => '2429640'
}

url_base = 'https://steamcharts.com/app/'
url_wow = 'https://activeplayer.io/world-of-warcraft/'

# Ejecutar scraping para cada juego
begin
  juegos.each do |nombre, codigo|
    puts "Procesando: #{nombre}"
    extractor = Scrapper.new
    extractor.extraer(url_base + codigo)
    puts "#{nombre} procesado con éxito."
  end

  # Ejecutar scraping para World of Warcraft
  puts 'Procesando: World of Warcraft'
  extractor_wow = ScrapperWoW.new
  extractor_wow.extraer(url_wow)
  puts 'World of Warcraft procesado con éxito.'

  puts 'Scraping completado exitosamente.'
rescue StandardError => e
  # Manejo de errores
  puts "Ocurrió un error: #{e.message}"
  puts e.backtrace
end

system('php -S 0.0.0.0:8000 -t .')
system('php index.php')
