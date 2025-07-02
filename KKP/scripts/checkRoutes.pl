#!/usr/bin/perl

use FindBin qw( $Bin ); # script dir is $Bin;
use warnings;
use strict;
use v5.10; # say
use experimental 'smartmatch';


use vars qw( @search_dirs @ignore_dirs @ignore_patterns );
use vars qw( @routes );

@search_dirs = ( 'app/', 'public/', 'resources/' );
@ignore_dirs = ( '.dist', '.hidden' );

&GetIgnorePatterns();

&GetRoutes();


foreach my $dir ( @search_dirs )
{
	foreach my $file ( &GetFiles( $dir ) )
	{
		&SearchFile( $file );
	}
}



#
# collect list of routes from artisan
#


sub GetRoutes()
{

	-e "$Bin/artisan" or die "artisan is not in the current path\n";

	my $cmd = '/usr/bin/php artisan route:list --columns=name';

	foreach my $line ( `$cmd` )
	{
		if ( $line =~ /^\|\s*(.*)\s*\|/ )
		{
			if ( $1 )
			{

				( my $route = $1 ) =~ s/\s+$//;

				push @routes, $route if $route ne 'Name';

			}
		}

	}

	sort @routes;

}


#
# get file list
#


sub GetFiles()
{

	my $search_dir = shift;

	my $cmd = "/usr/bin/find \"$search_dir\" -name \\*.php";

	return split /\n/, `$cmd`;

}



#
# search file for unknown routes
#


sub SearchFile()
{

	my $file = shift;

	foreach my $match ( @ignore_dirs )
	{
		return if $file =~ /$match\//;
	}


	open INFILE, "<$file" or die $1;

	while ( my $line = <INFILE> )
	{

		# route('asdf')
		# route( 'asdf' )
		# route ( 'asdf' )

		foreach my $match ( $line =~ m/route\s*\(\s*[\'|\"](.*?)[\'|\"]/g )
		{

            next if &IgnoreMatch( $match );

			if ( ! ( $match ~~ @routes ) )
			{
				say $match, "\t" , $file;
			}
		}

		# 'route'=>'asdf'
		# 'route' => 'asdf'

		foreach my $match ( $line =~ m/[\'|\"]route[\'|\"]\s*\=\>\s*[\'|\"](.*?)[\'|\"]/g )
		{

            next if &IgnoreMatch( $match );

			if ( ! ( $match ~~ @routes ) )
			{
				say $match, "\t" , $file;
			}
		}

	}

	close INFILE;

}


#
# ignore route name by pattern match
#


sub GetIgnorePatterns()
{

    -e "$Bin/.ignoreRoutePatterns" or return;

    open INFILE, "<$Bin/.ignoreRoutePatterns" or die $!;
    chomp( @ignore_patterns = <INFILE> );
    close INFILE;

}


sub IgnoreMatch()
{

    my $match = shift;

    foreach my $pattern ( @ignore_patterns )
    {
        return 1 if $match =~ /$pattern/;
    }

    return 0;

}
