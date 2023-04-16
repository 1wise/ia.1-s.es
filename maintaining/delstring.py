input_file = 'installed.txt'
output_file = 'toinstall.txt'

with open(input_file, 'r') as file:
    lines = file.readlines()

with open(output_file, 'w') as file:
    for line in lines:
        cleaned_line = line.split('/')[0] + '\n'
        file.write(cleaned_line)

